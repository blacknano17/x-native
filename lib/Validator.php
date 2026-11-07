<?php

class Validator
{
    private $data;
    private $rules;
    private $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    /**
     * Jalankan validasi
     */
    private function validate()
    {
        foreach ($this->rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $val = $this->data[$field] ?? null;

            foreach ($ruleList as $ruleItem) {
                $params = [];
                if (strpos($ruleItem, ':') !== false) {
                    [$ruleName, $paramStr] = explode(':', $ruleItem, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $ruleItem;
                }

                $ruleName = trim($ruleName);

                // Jika tidak required dan nilainya kosong/null, lewati aturan lainnya
                if ($ruleName !== 'required' && ($val === null || $val === '')) {
                    continue;
                }

                switch ($ruleName) {
                    case 'required':
                        if ($val === null || (is_string($val) && trim($val) === '') || (is_array($val) && empty($val))) {
                            $this->addError($field, "Field '$field' wajib diisi.");
                        }
                        break;

                    case 'string':
                        if (!is_string($val)) {
                            $this->addError($field, "Field '$field' harus berupa teks.");
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($val)) {
                            $this->addError($field, "Field '$field' harus berupa angka.");
                        }
                        break;

                    case 'integer':
                        if (filter_var($val, FILTER_VALIDATE_INT) === false) {
                            $this->addError($field, "Field '$field' harus berupa bilangan bulat.");
                        }
                        break;

                    case 'email':
                        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Format email pada '$field' tidak valid.");
                        }
                        break;

                    case 'min':
                        $min = (int)($params[0] ?? 0);
                        if (is_numeric($val) && $val < $min) {
                            $this->addError($field, "Field '$field' minimal bernilai $min.");
                        } elseif (is_string($val) && mb_strlen($val) < $min) {
                            $this->addError($field, "Field '$field' minimal sepanjang $min karakter.");
                        }
                        break;

                    case 'max':
                        $max = (int)($params[0] ?? 0);
                        if (is_numeric($val) && $val > $max) {
                            $this->addError($field, "Field '$field' maksimal bernilai $max.");
                        } elseif (is_string($val) && mb_strlen($val) > $max) {
                            $this->addError($field, "Field '$field' maksimal sepanjang $max karakter.");
                        }
                        break;

                    case 'in':
                        if (!in_array((string)$val, $params, true)) {
                            $this->addError($field, "Nilai '$field' harus salah satu dari: " . implode(', ', $params));
                        }
                        break;

                    case 'url':
                        if (!filter_var($val, FILTER_VALIDATE_URL)) {
                            $this->addError($field, "URL pada '$field' tidak valid.");
                        }
                        break;

                    case 'alpha_num':
                        if (!ctype_alnum(str_replace([' ', '_', '-'], '', (string)$val))) {
                            $this->addError($field, "Field '$field' hanya boleh berisi alfanumerik.");
                        }
                        break;
                }
            }
        }
    }

    private function addError(string $field, string $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Shortcut untuk validasi langsung dan otomatis melempar HTTP 422 jika gagal
     */
    public static function check(array $data, array $rules): array
    {
        $v = new self($data, $rules);
        if ($v->fails()) {
            Response::error('Validasi input gagal.', 422, $v->errors());
        }
        return $data;
    }
}
