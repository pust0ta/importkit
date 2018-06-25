<?php

class ImportkitParserCML extends ImportkitXMLReader
{
    public $file_size = 0;
    public $current_size = 0;
    public $current_ratio = -1;

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function parse()
    {
        $line = 0;
        $ver = '';
        $created = '';
        $only_changed = false;
        $name = '';
        $path = $this->getPath();
        $this->amount = 100000;
        $this->current = 0;
        //$this->current_ratio = -1;
        $this->open($path);
        while (!$this->stop() && $this->read()) {

            // Поиск версии обрабатываемого файла
            if ($this->name == 'КоммерческаяИнформация' && $this->nodeType == XMLReader::ELEMENT) {
                $ver = $this->getAttribute('ВерсияСхемы');
                $created = $this->getAttribute('ДатаФормирования');
                continue;
            }

            if ($this->name == 'ПакетПредложений' && $this->nodeType == XMLReader::ELEMENT) {
                $only_changed = $this->getAttribute('СодержитТолькоИзменения');
                continue;
            }

            $name = $this->name;

            if ($this->nodeType == XMLReader::ELEMENT && $this->isEmptyElement) {
                continue;
            }

            if (!in_array($this->nodeType, array(XMLReader::ELEMENT))){
                continue;
            }

            $params = array(
                // Объект XMLReader поставляется в модуль обработчик
                'reader' => $this,
                // Путь до обрабатываемого файла
                'path' => $path,
                // Версия схемы
                'ver' => $ver,
                // Дата создания
                'created' => $created,
                // Флаг только изменения
                'only_changed' => $only_changed,
                // Имя узла
                'name' => $name,
            );

            $this->setOption('params', array_merge($params, $this->getOption('params')));
            $this->exeCallBack();

        }
        $this->close();
    }

    public function trace($current = 1)
    {

        if (cache_get('importkit_progressive') && !cache_get('importkit_silent'))
        {
            $this->current += $current;
            $ratio = round($this->current / $this->amount, 2);
            if($ratio == 1){
                $this->current = 0;
            }
            importkit_print_progress($ratio);
        }

    }
}
