<?php

namespace Framework\Core;

/**
 * Clase Validator
 * Se encarga de validar arrays de datos (normalmente $_POST o $_GET)
 * basándose en reglas predefinidas.
 */
class Validator
{
    private $data;
    private $errors = [];
    private $rules = [
        'required' => 'El campo :field es obligatorio.',
        'email'    => 'El campo :field debe ser un email válido.',
        'min'      => 'El campo :field debe tener al menos :param caracteres.',
        'max'      => 'El campo :field no debe superar los :param caracteres.',
        'numeric'  => 'El campo :field debe ser un número.',
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Aplica reglas de validación.
     * Ejemplo: $v->validate(['username' => 'required|min:5', 'email' => 'required|email']);
     */
    public function validate(array $validationRules)
    {
        foreach ($validationRules as $field => $rules) {
            $rulesArray = explode('|', $rules);

            foreach ($rulesArray as $rule) {
                $params = [];
                
                // Manejar reglas con parámetros como min:5
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramValue) = explode(':', $rule);
                    $params[] = $paramValue;
                }

                $methodName = 'validate' . ucfirst($rule);
                $value = $this->data[$field] ?? null;

                if (method_exists($this, $methodName)) {
                    if (!$this->$methodName($value, $params)) {
                        $this->addError($field, $rule, $params[0] ?? null);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    // --- Reglas de Validación ---

    protected function validateRequired($value)
    {
        return !empty($value) || $value === '0';
    }

    protected function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin($value, $params)
    {
        return strlen($value) >= (int)$params[0];
    }

    protected function validateMax($value, $params)
    {
        return strlen($value) <= (int)$params[0];
    }

    protected function validateNumeric($value)
    {
        return is_numeric($value);
    }

    // --- Gestión de Errores ---

    private function addError($field, $rule, $param = null)
    {
        $message = $this->rules[$rule] ?? "El campo :field no es válido.";
        $message = str_replace(':field', $field, $message);
        if ($param) {
            $message = str_replace(':param', $param, $message);
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError($field)
    {
        return $this->errors[$field][0] ?? null;
    }
}