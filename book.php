<?php

class Book
{
    private string $title;
    private string $author;
    private string $isbn;
    private bool $available = true;

    private static $isbns = array();

    public function get_title(): string
    {
        return $this->title;
    }

    public function set_title(string $title): object
    {
        if (!empty($title) || !ctype_space($title))
        {
            $this->title = $title;
            return $this;
        }
        throw new Exception("Атрибут title - пустая строка");
    }

    public function get_author(): string
    {
        return $this->author;
    }

    public function set_author(string $author): object
    {
        if (!empty($author) || !ctype_space($author))
        {
            $this->author = $author;
            return $this;
        }
        throw new Exception("Атрибут author - пустая строка");
    }

    public function get_isbn(): string
    {
        return $this->isbn;
    }

    public function set_isbn(string $isbn): object
    {
        if (in_array($isbn, self::$isbns))
        {
            throw new Exception("Книга с ISBN $isbn уже существует");
        }

        if ($this->validate_isbn($isbn))
        {
            $this->isbn = $isbn;
            return $this;
        }

        throw new Exception("Неверный формат ISBN $isbn");
    }

    public function get_available(): bool
    {
        return $this->available;
    }

    public function set_available(bool $available): object
    {
        $this->available = $available;
        return $this;
    }

    private function validate_isbn(string $isbn): bool
    {
        $separator = "-";
        $codes_count = 5;
        $isbn_code = "";

        $registration_group_number = [0, 1, 2, 3, 4, 5, 7, 80, 600, 953, 966, 985, 9956, 99948];

        /* По ГОСТу количество кодов в ISBN должно быть 5.
        Значит, количество дефисов меньше на единицу */
        if (substr_count($isbn, $separator) == $codes_count-1)
        {
            $isbn_codes = explode($separator, $isbn);
            if (count($isbn_codes) == $codes_count)
            {
                for ($i = 0; $i < $codes_count; $i++)
                {
                    if (!is_numeric($isbn_codes[$i]))
                    {
                        return false;
                    }

                    switch($i)
                    {
                        case 0:
                            if ($isbn_codes[$i] == 978)
                            {
                                break;
                            }
                            return false;
                        case 1:
                            if (in_array($isbn_codes[$i], $registration_group_number))
                            {
                                break;
                            }
                            return false;
                        case 2:
                            if (strlen($isbn_codes[$i]) >= 2 && strlen($isbn_codes[$i]) <= 6)
                            {
                                break;
                            }
                            return false;
                        case 3:
                            if ((strlen($isbn_codes[$i]) >= 2 && strlen($isbn_codes[$i]) <= 6) && ($isbn_codes[$i] > $isbn_codes[$i-1] || $isbn_codes[$i] < $isbn_codes[$i-1]))
                            {
                                break;
                            }
                            return false;
                        case 4:
                            $even_sum = 0;
                            $odd_sum = 0;

                            for ($j = 0; $j < $codes_count-1; $j++)
                            {
                                if ($j % 2 == 0)
                                {
                                    $even_sum += $isbn_code[$j];
                                } 
                                else
                                {
                                    $odd_sum += $isbn_code[$j];
                                }
                            }

                            $digit = ($odd_sum*3 + $even_sum)%10;
                            
                            if ($digit >= 0)
                            {
                                if (($digit == 0 && $isbn_codes[$i] == $digit) ||
                                ($isbn_codes[$i] == 10 - $digit))
                                {
                                    return true;
                                }
                            }
                            return false;
                    }
                    $isbn_code  .= $isbn_codes[$i];
                }
            }
        }
        return false;
    }
}