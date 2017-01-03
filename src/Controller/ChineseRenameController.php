<?php
namespace Drupal\chinese_rename\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChineseRenameController extends ControllerBase {
  // 重命名中文文件名
  public static function rename_chinese_filename(&$element, $input, FormStateInterface $form_state) {
    # 如果提交新的文件
    if(!is_numeric($input['fids'])) {
      $form_field_name = implode('_', $element['#parents']);
      $all_files = \Drupal::request()->files->get('files', array());
      
      // Make sure there's an upload to process.
      if (empty($all_files[$form_field_name])) {
        return NULL;
      }
      $file_upload = $all_files[$form_field_name];
      // Prepare uploaded files info. Representation is slightly different
      // for multiple uploads and we fix that here.
      $uploaded_files = $file_upload;
      if (!is_array($file_upload)) {
        $uploaded_files = array($file_upload);
      }
      $files = array();
      $destination = 'temporary://';
      $realPath = \Drupal::service('file_system')->realpath($destination);
      foreach ($uploaded_files as $i => $file_info) {
        # 把源文件对象替换成重命名过的文件对象
        $originalName = $file_info->getClientOriginalName();
        if(preg_match("/[\x7f-\xff]/", $originalName)) {
          $newName = md5($originalName) . '.' . $file_info->getClientOriginalExtension();
          # 新建一个上传文件对象
          $newFile = new UploadedFile(
            $file_info->getRealPath(),
            $newName,
            $file_info->getClientMimeType(),
            $file_info->getClientSize(),
            $file_info->getError()
          );

          if(count($uploaded_files) <= 1) {
            $uploaded_files = $newFile;
          } else {
            $uploaded_files[$i] = $newFile;
          }
        }
      }
      $all_files[$form_field_name] = $uploaded_files;
      \Drupal::request()->files->set('files', $all_files);
    }
    
    # 调用默认文件处理方法
    return \Drupal\file\Plugin\Field\FieldWidget\FileWidget::value($element, $input, $form_state);
  }
}