require 'fileutils'

sphinx_config = File.__DIR__ + '/../../../config/sphinx.yml'
FileUtils.cp File.__DIR__ + '/sphinx.yml.tpl', sphinx_config unless File.exist?(sphinx_config)
puts IO.read(File.join(File.__DIR__, 'README'))
