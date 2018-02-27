<?php

namespace ipinfo\helpers;

use mmvc\models\BaseModel;

/**
 * Вывод информации о переменной либо передача информации в массив
 * @package ipinfo\helpers
 */
class VarDumper extends BaseModel
{

    /**
     * Получение инфы о передаваемой переменной в виде массива varname ['type' => , 'value' => ]
     * @param mixed $data
     * @param string $name имя свойства для вывода (используется только для корня)
     * @param string $type тип передаваемой переменной (если известно заранее)
     * @return array
     */
    public static function getData(&$data, $name, $type = null)
    {
        if ($type === null)
        {
            $type = gettype($data);
        }

        $result = [];

        switch ($type)
        {
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                foreach ($data as $key => $value)
                {
                    $type = gettype($value);
                    $tmp = self::getData($value, $key, $type);
                    if (!empty($tmp[$key])) {
                        $result[$key] = $tmp[$key];
                    } else if (count($tmp) > 1) {
                        $result[$key] = $tmp;
                    }
                }
                break;
            case 'resource':
                $result[$name] = [
                    'type' => $type,
                    'value' => (int)$data
                ];
                break;
            default:
                $result[$name] = [
                    'type' => $type,
                    'value' => $data
                ];
                break;
        }
        return $result;

    }

    /**
     * Форматирование отступов для текущего уровня вложенности
     * Используется в методе printData
     * @param integer $count количество отступов
     * @see VarDumper::printData()
     */
    private static function printTabs($count)
    {
        $i = -1;
        while($i < $count)
        {
            echo "    ";
            $i++;
        }
    }

    /**
     * Дамп информации о переменной
     * @param array $data результат выполнения getData
     * @param string $name имя переменной 
     * @param int $level уровень вложенности
     * @throws \Exception выбрасывается если в параметр $data не передана ссылка на массив
     * @see VarDumper::getData()
     */
    public static function printData(&$data, $name = '', $level = 0)
    {
        if (!is_array($data))
        {
            throw new \Exception('$data is not array');
        }

        if ($level === 0)
        {
            echo "<pre class='ipinfo-vardumper' dir='ltr'>";
        }

        echo "<b>array</b> <i>$name (size=".count($data).")</i>\n";

        foreach ($data as $key => &$value)
        {
            echo self::printTabs($level);
            if (empty($value['type']) || $value['type'] === 'array') {
                self::printData($value, $key, $level + 1);               
            } else {
                $valueColor = '#888a85';
                $addition = '';
                echo "'$key' ";
                switch ($value['type']) {
                    case 'string':
                        $addition = "<i>(length=" . strlen($value['value']) . ")</i>";
                        $value['value'] = "'{$value['value']}'";
                        $valueColor = '#cc0000';
                        break;
                    case 'float':
                    case 'double':
                        $valueColor = '#f57900';
                        break;
                    case 'integer':
                        $valueColor = '#4e9a06';
                        break;
                }
                echo "<font color='#888a85'>=&gt;</font>";
                echo " <small>{$value['type']}</small> ";
                echo "<font color='$valueColor'>{$value['value']}</font>";
                echo "$addition\n";
            }
        }

        if ($level === 0)
        {
            echo "</pre>";
        }
    }

}