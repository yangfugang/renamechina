#Chinese Rename
#Drupal 8 上传文件时自动重命名中文文件名模块。
为了解决Windows平台下上传中文名文件时，move_uploaded_file出错的问题。
为表单的文件字段设置#value_callback属性，检测到中文文件名时，在保存文件之前，修改上传文件的原始名称，使用MD5编码的方式，也可以改成别的方法。
