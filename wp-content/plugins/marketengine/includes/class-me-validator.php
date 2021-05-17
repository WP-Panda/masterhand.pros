<?php

/**
 * The AE_Validator class
 *
 * Class is used for the purpose of checking the data is valid before they are stored in the database
 *
 * @since  1.0
 * @package MarketEngine/Includes
 * @category Class
 * @author nguyenvanduocit
 */
class ME_Validator {
    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = array();
    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;
    /**
     * The files under validation.
     *
     * @var array
     */
    protected $files = array();
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;
    /**
     * The array of custom error messages.
     *
     * @var array
     */
    protected $customMessages = array();

    protected $messages = array();
    /**
     * The array of fallback error messages.
     *
     * @var array
     */
    protected $fallbackMessages = array();
    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    protected $customAttributes = array();
    /**
     * The array of custom displayabled values.
     *
     * @var array
     */
    protected $customValues = array();
    /**
     * All of the custom validator extensions.
     *
     * @var array
     */
    protected $extensions = array();
    /**
     * All of the custom replacer extensions.
     *
     * @var array
     */
    protected $replacers = array();
    /**
     * The size related validation rules.
     *
     * @var array
     */
    protected $sizeRules = array('Size', 'Between', 'Min', 'Max', 'GreaterThan', 'LessThan');
    /**
     * The numeric related validation rules.
     *
     * @var array
     */
    protected $numericRules = array('Numeric', 'Integer', 'numeric', 'integer', 'int');
    /**
     * The validation rules that imply the field is required.
     *
     * @var array
     */
    protected $implicitRules = array(
        'Required',
        'RequiredWith',
        'RequiredWithAll',
        'RequiredWithout',
        'RequiredWithoutAll',
        'RequiredIf',
        'Accepted',
    );

    /**
     * Create a new Validator instance.
     ** @param  array $data
     *
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     *
     * @return void
     */
    public function __construct(array $data, array $rules, array $customAttributes = array(), array $customMessages = array()) {
        $this->data = $this->parseData($data);
        $this->rules = $this->explodeRules($rules);
        $this->customAttributes = $customAttributes;
        $messages = array(
            'accepted' => __("The :attribute must be accepted.", "enginethemes"),
            'active_url' => __("The :attribute is not a valid URL.", "enginethemes"),
            'after' => __("The :attribute must be a date after :date.", "enginethemes"),
            'alpha' => __("The :attribute may only contain letters.", "enginethemes"),
            'alpha_dash' => __('The :attribute may only contain letters, numbers, and dashes.', "enginethemes"),
            'alpha_num' => __('The :attribute may only contain letters and numbers.', "enginethemes"),
            'array' => __('The :attribute must be an array.', "enginethemes"),
            'before' => __('The :attribute must be a date before :date.', "enginethemes"),
            'between' => array(
                'numeric' => __('The :attribute must be between :min and :max.', "enginethemes"),
                'file' => __('The :attribute must be between :min and :max kilobytes.', "enginethemes"),
                'string' => __('The :attribute must be between :min and :max characters.', "enginethemes"),
                'array' => __('The :attribute must have between :min and :max items.', "enginethemes"),
            ),
            'boolean' => __('The :attribute field must be true or false.', "enginethemes"),
            'confirmed' => __('The :attribute confirmation does not match.', "enginethemes"),
            'date' => __('The :attribute is not a valid date.', "enginethemes"),
            'date_format' => __('The :attribute does not match the format :format.', "enginethemes"),
            'different' => __('The :attribute and :other must be different.', "enginethemes"),
            'digits' => __('The :attribute must be :digits digits.', "enginethemes"),
            'digits_between' => __('The :attribute must be between :min and :max digits.', "enginethemes"),
            'email' => __('The :attribute must be a valid email address.', "enginethemes"),
            'filled' => __('The :attribute field is required.', "enginethemes"),
            'exists' => __('The selected :attribute is invalid.', "enginethemes"),
            'image' => __('The :attribute must be an image.', "enginethemes"),
            'in' => __('The selected :attribute is invalid.', "enginethemes"),
            'integer' => __('The :attribute must be an integer.', "enginethemes"),
            'ip' => __('The :attribute must be a valid IP address.', "enginethemes"),
            'max' => array(
                'numeric' => __('The :attribute may not be greater than :max.', "enginethemes"),
                'file' => __('The :attribute may not be greater than :max kilobytes.', "enginethemes"),
                'string' => __('The :attribute may not be greater than :max characters.', "enginethemes"),
                'array' => __('The :attribute may not have more than :max items.', "enginethemes"),
            ),
            'mimes' => __('The :attribute must be a file of type: :values.', "enginethemes"),
            'min' => array(
                'numeric' => __('The :attribute must be at least :min.', "enginethemes"),
                'file' => __('The :attribute must be at least :min kilobytes.', "enginethemes"),
                'string' => __('The :attribute must be at least :min characters.', "enginethemes"),
                'array' => __('The :attribute must have at least :min items.', "enginethemes"),
            ),
            'not_in' => __('The selected :attribute is invalid.', "enginethemes"),
            'numeric' => __('The :attribute must be a number.', "enginethemes"),
            'regex' => __('The :attribute format is invalid.', "enginethemes"),
            'required' => __('The :attribute field is required.', "enginethemes"),
            'required_if' => __('The :attribute field is required when :other is :value.', "enginethemes"),
            'required_with' => __('The :attribute field is required when :values is present.', "enginethemes"),
            'required_with_all' => __('The :attribute field is required when :values is present.', "enginethemes"),
            'required_without' => __('The :attribute field is required when :values is not present.', "enginethemes"),
            'required_without_all' => __('The :attribute field is required when none of :values are present.', "enginethemes"),
            'same' => __('The :attribute and :other must match.', "enginethemes"),
            'size' => array(
                'numeric' => __('The :attribute must be :size.', "enginethemes"),
                'file' => __('The :attribute must be :size kilobytes.', "enginethemes"),
                'string' => __('The :attribute must be :size characters.', "enginethemes"),
                'array' => __('The :attribute must contain :size items.', "enginethemes"),
            ),
            'greater_than' => __("The :attribute must be greater than :greater_than.", "enginethemes"),
            'timezone' => __('The :attribute must be a valid zone.', "enginethemes"),
            'unique' => __('The :attribute has already been taken.', "enginethemes"),
            'url' => __('The :attribute format is invalid.', "enginethemes")
        );

        $this->customMessages = apply_filters('marketengine_validator_messages', $messages);
    }

    /**
     * Parse the data and hydrate the files array.
     *
     * @param  array  $data
     * @param  string $arrayKey
     *
     * @return array
     */
    protected function parseData(array $data, $arrayKey = NULL) {
        return $data;
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array $rules
     *
     * @return array
     */
    protected function explodeRules($rules) {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * Define a set of rules that apply to each element in an array attribute.
     *
     * @param  string       $attribute
     * @param  string|array $rules
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    public function each($attribute, $rules) {
        $data = static::array_get($this->data, $attribute);
        if (!is_array($data)) {
            if ($this->hasRule($attribute, 'Array')) {
                return;
            }
            throw new InvalidArgumentException('Attribute for each() must be an array.');
        }
        foreach ($data as $dataKey => $dataValue) {
            foreach ($rules as $ruleKey => $ruleValue) {
                if (!is_string($ruleKey)) {
                    $this->mergeRules("$attribute.$dataKey", $ruleValue);
                } else {
                    $this->mergeRules("$attribute.$dataKey.$ruleKey", $ruleValue);
                }
            }
        }
    }

    /**
     * Merge additional rules into a given attribute.
     *
     * @param  string       $attribute
     * @param  string|array $rules
     *
     * @return void
     */
    public function mergeRules($attribute, $rules) {
        $current = isset($this->rules[$attribute]) ? $this->rules[$attribute] : array();
        $merge = reset($this->explodeRules(array($rules)));
        $this->rules[$attribute] = array_merge($current, $merge);
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes() {
        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->validate($attribute, $rule);
            }
        }

        return count($this->messages) === 0;
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails() {
        return !$this->passes();
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param  string $attribute
     * @param  string $rule
     *
     * @return void
     */
    protected function validate($attribute, $rule) {
        list($rule, $parameters) = $this->parseRule($rule);
        if ($rule == '') {
            return;
        }
        // We will get the value for the given attribute from the array of data and then
        // verify that the attribute is indeed validatable. Unless the rule implies
        // that the attribute is required, rules are not run for missing values.
        $value = $this->getValue($attribute);
        $rule = ucfirst($rule);
        if (is_array($value) && !$this->isArrayAccepted($rule)) {
            foreach ($value as $val) {
                $validatable = $this->isValidatable($rule, $attribute, $val);
                $method = "validate{$rule}";
                if ($validatable && !$this->$method($attribute, $val, $parameters, $this)) {
                    $this->addFailure($attribute, $rule, $parameters);
                }
            }
        } else {
            $validatable = $this->isValidatable($rule, $attribute, $value);
            $method = "validate{$rule}";
            if ($validatable && !$this->$method($attribute, $value, $parameters, $this)) {
                $this->addFailure($attribute, $rule, $parameters);
            }
        }
    }

    /**
     * Returns the data which was valid.
     *
     * @return array
     */
    public function valid() {
        if (!$this->messages) {
            $this->passes();
        }

        return array_diff_key($this->data, $this->messages);
    }

    /**
     * Returns the data which was invalid.
     *
     * @return array
     */
    public function invalid() {
        if (!$this->messages) {
            $this->passes();
        }

        return array_intersect_key($this->data, $this->messages);
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string $attribute
     *
     * @return mixed
     */
    protected function getValue($attribute) {
        if (!is_null($value = static::array_get($this->data, $attribute))) {
            return $value;
        } elseif (!is_null($value = static::array_get($this->files, $attribute))) {
            return $value;
        }
    }

    /**
     * Determine if the attribute is validatable.
     *
     * @param  string $rule
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function isValidatable($rule, $attribute, $value) {
        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
        $this->passesOptionalCheck($attribute) &&
        $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute);
    }

    /**
     * This method check if can mergeValue for one validate for array value.
     *
     * @since  0.9.0
     * @return bool
     * @author nguyenvanduocit
     */
    protected function isArrayAccepted($rule) {
        return in_array($rule, array('Unique', 'Exists'));
    }

    /**
     * Determine if the field is present, or the rule implies required.
     *
     * @param  string $rule
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function presentOrRuleIsImplicit($rule, $attribute, $value) {
        return $this->validateRequired($attribute, $value) || $this->isImplicit($rule);
    }

    /**
     * Determine if the attribute passes any optional check.
     *
     * @param  string $attribute
     *
     * @return bool
     */
    protected function passesOptionalCheck($attribute) {
        if ($this->hasRule($attribute, array('Sometimes'))) {
            return array_key_exists($attribute, static::dot($this->data))
            || in_array($attribute, array_keys($this->data))
            || array_key_exists($attribute, $this->files);
        }

        return TRUE;
    }
    public static function dot($array, $prepend = '') {
        $results = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }
    /**
     * Determine if a given rule implies the attribute is required.
     *
     * @param  string $rule
     *
     * @return bool
     */
    protected function isImplicit($rule) {
        return in_array($rule, $this->implicitRules);
    }

    /**
     * Determine if it's a necessary presence validation.
     * This is to avoid possible database type comparison errors.
     *
     * @param  string $rule
     * @param  string $attribute
     *
     * @return bool
     */
    protected function hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute) {
        return in_array($rule, array('Unique', 'Exists', 'Nonce'))
        ? !array_key_exists($attribute, $this->messages) : TRUE;
    }

    /**
     * Add a failed rule and error message to the collection.
     *
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return void
     */
    protected function addFailure($attribute, $rule, $parameters) {
        $this->addError($attribute, $rule, $parameters);
        $this->failedRules[$attribute][$rule] = $parameters;
    }

    /**
     * Add an error message to the validator's collection of messages.
     *
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return void
     */
    protected function addError($attribute, $rule, $parameters) {
        $message = $this->getMessage($attribute, $rule);
        $message = $this->doReplacements($message, $attribute, $rule, $parameters);
        if (is_array($message)) {
            $type = $this->getAttributeType($attribute);
            $message = $message[$type];
        }
        $this->messages[$attribute] = $message;
    }

    /**
     * "Validate" optional attributes.
     * Always returns true, just lets us put sometimes in rules.
     *
     * @return bool
     */
    protected function validateSometimes() {
        return TRUE;
    }

    /**
     * Validate that a required attribute exists.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateRequired($attribute, $value) {
        if (is_null($value)) {
            return FALSE;
        } elseif (is_string($value) && trim($value) === '') {
            return FALSE;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate the given attribute is filled if it is present.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateFilled($attribute, $value) {
        if (array_key_exists($attribute, $this->data) || array_key_exists($attribute, $this->files)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Determine if any of the given attributes fail the required test.
     *
     * @param  array $attributes
     *
     * @return bool
     */
    protected function anyFailingRequired(array $attributes) {
        foreach ($attributes as $key) {
            if (!$this->validateRequired($key, $this->getValue($key))) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Determine if all of the given attributes fail the required test.
     *
     * @param  array $attributes
     *
     * @return bool
     */
    protected function allFailingRequired(array $attributes) {
        foreach ($attributes as $key) {
            if ($this->validateRequired($key, $this->getValue($key))) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Validate that an attribute exists when any other attribute exists.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     *
     * @return bool
     */
    protected function validateRequiredWith($attribute, $value, $parameters) {
        if (!$this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Validate that an attribute exists when all other attributes exists.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     *
     * @return bool
     */
    protected function validateRequiredWithAll($attribute, $value, $parameters) {
        if (!$this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Validate that an attribute exists when another attribute does not.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     *
     * @return bool
     */
    protected function validateRequiredWithout($attribute, $value, $parameters) {
        if ($this->anyFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Validate that an attribute exists when all other attributes do not.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     *
     * @return bool
     */
    protected function validateRequiredWithoutAll($attribute, $value, $parameters) {
        if ($this->allFailingRequired($parameters)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Validate that an attribute exists when another attribute has a given value.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  mixed  $parameters
     *
     * @return bool
     */
    protected function validateRequiredIf($attribute, $value, $parameters) {
        $this->requireParameterCount(2, $parameters, 'required_if');
        $data = static::array_get($this->data, $parameters[0]);
        $values = array_slice($parameters, 1);
        if (in_array($data, $values)) {
            return $this->validateRequired($attribute, $value);
        }

        return TRUE;
    }

    /**
     * Get the number of attributes in a list that are present.
     *
     * @param  array $attributes
     *
     * @return int
     */
    protected function getPresentCount($attributes) {
        $count = 0;
        foreach ($attributes as $key) {
            if (static::array_get($this->data, $key) || static::array_get($this->files, $key)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Validate that an attribute has a matching confirmation.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateConfirmed($attribute, $value) {
        return $this->validateSame($attribute, $value, array($attribute . '_confirmation'));
    }

    /**
     * Validate that two attributes match.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateSame($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'same');
        $other = static::array_get($this->data, $parameters[0]);

        return isset($other) && $value == $other;
    }

    /**
     * Validate that an attribute is different from another attribute.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateDifferent($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'different');
        $other = static::array_get($this->data, $parameters[0]);

        return isset($other) && $value != $other;
    }

    /**
     * Validate that an attribute was "accepted".
     * This validation rule implies the attribute is "required".
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateAccepted($attribute, $value) {
        $acceptable = array('yes', 'on', '1', 1, TRUE, 'true');

        return $this->validateRequired($attribute, $value) && in_array($value, $acceptable, TRUE);
    }

    /**
     * Validate that an attribute is an array.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateArray($attribute, $value) {
        return is_array($value);
    }

    /**
     * Validate that an attribute is a boolean.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateBoolean($attribute, $value) {
        $acceptable = array(TRUE, FALSE, 0, 1, '0', '1');

        return in_array($value, $acceptable, TRUE);
    }

    /**
     * Validate that an attribute is an integer.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateInteger($attribute, $value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== FALSE;
    }

    /**
     * Validate that an attribute is numeric.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateNumeric($attribute, $value) {
        return is_numeric($value);
    }

    /**
     * Validate that an attribute is a string.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateString($attribute, $value) {
        return is_string($value);
    }

    /**
     * Validate that an attribute has a given number of digits.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateDigits($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'digits');

        return $this->validateNumeric($attribute, $value)
        && strlen((string) $value) == $parameters[0];
    }

    /**
     * Validate that an attribute is between a given number of digits.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateDigitsBetween($attribute, $value, $parameters) {
        $this->requireParameterCount(2, $parameters, 'digits_between');
        $length = strlen((string) $value);

        return $this->validateNumeric($attribute, $value)
            && $length >= $parameters[0] && $length <= $parameters[1];
    }

    /**
     * Validate the size of an attribute.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateSize($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'size');

        return $this->getSize($attribute, $value) == $parameters[0];
    }

    /**
     * Validate the size of an attribute is between a set of values.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateBetween($attribute, $value, $parameters) {
        $this->requireParameterCount(2, $parameters, 'between');
        $size = $this->getSize($attribute, $value);

        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateMin($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'min');

        return $this->getSize($attribute, $value) >= $parameters[0];
    }

    protected function validateGreaterThan($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'greater_than');

        return $this->getSize($attribute, $value) > $parameters[0];
    }


    /**
     * Validate the size of an attribute is less than a maximum value.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateMax($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'max');
        return $this->getSize($attribute, $value) <= $parameters[0];
    }

    /**
     * Get the size of an attribute.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function getSize($attribute, $value) {
        $hasNumeric = $this->hasRule($attribute, $this->numericRules);
        // This method will determine if the attribute is a number, string, or file and
        // return the proper size accordingly. If it is a number, then number itself
        // is the size. If it is a file, we take kilobytes, and for a string the
        // entire length of the string will be considered the attribute size.
        if (is_numeric($value) && $hasNumeric) {
            return static::array_get($this->data, $attribute);
        } elseif (is_array($value)) {
            return count($value);
        }

        return mb_strlen($value);
    }

    /**
     * Validate an attribute is contained within a list of values.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateIn($attribute, $value, $parameters) {
        return in_array((string) $value, $parameters);
    }

    /**
     * Validate an attribute is not contained within a list of values.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateNotIn($attribute, $value, $parameters) {
        return !$this->validateIn($attribute, $value, $parameters);
    }

    /**
     * Validate the uniqueness of an attribute value on a given database table.
     * If a database column is not specified, the attribute will be used.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateUnique($attribute, $value, $parameters) {
        return !$this->validateExists($attribute, $value, $parameters);
    }

    /**
     * Validate the existence of an attribute value in a database table.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateExists($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'exists');
        $table = $parameters[0];
        // The second parameter position holds the name of the column that should be
        // verified as existing. If this parameter is not specified we will guess
        // that the columns being "verified" shares the given attribute's name.
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;
        $expected = (is_array($value)) ? count($value) : 1;

        return $this->getExistCount($table, $column, $value, $parameters) >= $expected;
    }

    /**
     * Validate wordpress nonce.
     *
     * @since  0.9.0
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return bool
     * @author nguyenvanduocit
     */
    protected function validateNonce($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'nonce');
        $action = $parameters[0];
        if (function_exists('wp_verify_nonce')) {
            return wp_verify_nonce($value, $action);
        } else {
            return FALSE;
        }
    }

    protected function validateTerm($attribute, $value, $parameters) {
        $taxonomy = isset($parameters[0]) ? $parameters[0] : NULL;
        $parent = isset($parameters[1]) ? $parameters[1] : NULL;

        if (function_exists('term_exists')) {
            return (term_exists($value, $taxonomy, $parent) != NULL);
        } else {
            return FALSE;
        }
    }

    /**
     * Get the number of records that exist in storage.
     *
     * @param  string $table
     * @param  string $column
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return int
     */
    protected function getExistCount($table, $column, $value, $parameters) {
        global $wpdb;
        if (!is_array($value)) {
            $value = (array) $value;
        }

        $value_count = count($value);
        $stringPlaceholders = array_fill(0, $value_count, '%s');
        $placeholdersForValue = implode(',', $stringPlaceholders);

        $value = $wpdb->prepare($placeholdersForValue, $value);

        switch (count($parameters)) {
        case 4:
            $addition_column = $parameters[2];
            $addtion_value = $parameters[3];
            $query = $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} IN ({$value}) AND {$addition_column} = %s", $addtion_value);
            break;
        case 2:
        default:
            $query = "SELECT COUNT(*) FROM {$table} WHERE {$column} IN ({$value})";
            break;
        }
        $count = $wpdb->get_var($query);

        return $count;
    }

    /**
     * Get the extra exist conditions.
     *
     * @param  array $parameters
     *
     * @return array
     */
    protected function getExtraExistConditions(array $parameters) {
        return $this->getExtraConditions(array_values(array_slice($parameters, 2)));
    }

    /**
     * Get the extra conditions for a unique / exists rule.
     *
     * @param  array $segments
     *
     * @return array
     */
    protected function getExtraConditions(array $segments) {
        $extra = array();
        $count = count($segments);
        for ($i = 0; $i < $count; $i = $i + 2) {
            $extra[$segments[$i]] = $segments[$i + 1];
        }

        return $extra;
    }

    /**
     * Validate that an attribute is a valid IP.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateIp($attribute, $value) {
        return filter_var($value, FILTER_VALIDATE_IP) !== FALSE;
    }

    /**
     * Validate that an attribute is a valid e-mail address.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateEmail($attribute, $value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE;
    }

    /**
     * Validate that an attribute is a valid URL.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateUrl($attribute, $value) {
        return filter_var($value, FILTER_VALIDATE_URL) !== FALSE;
    }

    /**
     * Validate that an attribute is an active URL.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateActiveUrl($attribute, $value) {
        $url = str_replace(array('http://', 'https://', 'ftp://'), '', strtolower($value));

        return checkdnsrr($url, 'A');
    }
    /**
     * Validate the MIME type of a file is an image MIME type.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    /*protected function validateImage($attribute, $value)
    {
    return $this->validateMimes($attribute, $value, array('jpeg', 'png', 'gif', 'bmp', 'svg'));
    }*/
    /**
     * Validate the MIME type of a file upload attribute is in a set of MIME types.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    /*protected function validateMimes( $attribute, $value, $parameters ) {
    if ( ! $this->isAValidFileInstance( $value ) ) {
    return FALSE;
    }

    return $value->getPath() != '' && in_array( $value->guessExtension(), $parameters );
    }*/

    /**
     * Check that the given value is a valid file instance.
     *
     * @param  mixed $value
     *
     * @return bool
     */
    /*protected function isAValidFileInstance( $value ) {
    if ( $value instanceof UploadedFile && ! $value->isValid() ) {
    return FALSE;
    }

    return $value instanceof File;
    }*/

    /**
     * Validate that an attribute contains only alphabetic characters.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateAlpha($attribute, $value) {
        return preg_match('/^[\pL\pM]+$/u', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateAlphaNum($attribute, $value) {
        return preg_match('/^[\pL\pM\pN]+$/u', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateAlphaDash($attribute, $value) {
        return preg_match('/^[\pL\pM\pN_-]+$/u', $value);
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateRegex($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'regex');

        return preg_match($parameters[0], $value);
    }

    /**
     * Validate that an attribute is a valid date.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateDate($attribute, $value) {
        if ($value instanceof DateTime) {
            return TRUE;
        }
        if (strtotime($value) === FALSE) {
            return FALSE;
        }
        $date = date_parse($value);

        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * Validate that an attribute matches a date format.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateDateFormat($attribute, $value, $parameters) {
        $this->requireParameterCount(1, $parameters, 'date_format');
        $parsed = date_parse_from_format($parameters[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }
    /**
     * Validate the date is before a given date.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    /*protected function validateBefore($attribute, $value, $parameters)
    {
    $this->requireParameterCount(1, $parameters, 'before');
    if ($format = $this->getDateFormat($attribute))
    {
    return $this->validateBeforeWithFormat($format, $value, $parameters);
    }
    if ( ! ($date = strtotime($parameters[0])))
    {
    return strtotime($value) < strtotime($this->getValue($parameters[0]));
    }
    return strtotime($value) < $date;
    }*/
    /**
     * Validate the date is before a given date with a given format.
     *
     * @param  string $format
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateBeforeWithFormat($format, $value, $parameters) {
        $param = $this->getValue($parameters[0]) ?: $parameters[0];

        return $this->checkDateTimeOrder($format, $value, $param);
    }
    /**
     * Validate the date is after a given date.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    /*protected function validateAfter($attribute, $value, $parameters)
    {
    $this->requireParameterCount(1, $parameters, 'after');
    if ($format = $this->getDateFormat($attribute))
    {
    return $this->validateAfterWithFormat($format, $value, $parameters);
    }
    if ( ! ($date = strtotime($parameters[0])))
    {
    return strtotime($value) > strtotime($this->getValue($parameters[0]));
    }
    return strtotime($value) > $date;
    }*/
    /**
     * Validate the date is after a given date with a given format.
     *
     * @param  string $format
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    protected function validateAfterWithFormat($format, $value, $parameters) {
        $param = $this->getValue($parameters[0]) ?: $parameters[0];

        return $this->checkDateTimeOrder($format, $param, $value);
    }

    /**
     * Given two date/time strings, check that one is after the other.
     *
     * @param  string $format
     * @param  string $before
     * @param  string $after
     *
     * @return bool
     */
    protected function checkDateTimeOrder($format, $before, $after) {
        $before = $this->getDateTimeWithOptionalFormat($format, $before);
        $after = $this->getDateTimeWithOptionalFormat($format, $after);

        return ($before && $after) && ($after > $before);
    }

    /**
     * Get a DateTime instance from a string.
     *
     * @param  string $format
     * @param  string $value
     *
     * @return \DateTime|null
     */
    protected function getDateTimeWithOptionalFormat($format, $value) {
        $date = DateTime::createFromFormat($format, $value);
        if ($date) {
            return $date;
        }
        try {
            return new DateTime($value);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Validate that an attribute is a valid timezone.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateTimezone($attribute, $value) {
        try {
            new DateTimeZone($value);
        } catch (Exception $e) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get the date format for an attribute if it has one.
     *
     * @param  string $attribute
     *
     * @return string|null
     */
    protected function getDateFormat($attribute) {
        if ($result = $this->getRule($attribute, 'DateFormat')) {
            return $result[1][0];
        }
    }

    /**
     * Get the validation message for an attribute and rule.
     *
     * @param  string $attribute
     * @param  string $rule
     *
     * @return string
     */
    protected function getMessage($attribute, $rule) {
        $lowerRule = static::snake_case($rule);
        $inlineMessage = $this->getInlineMessage($attribute, $lowerRule);
        // First we will retrieve the custom message for the validation rule if one
        // exists. If a custom validation message is being used we'll return the
        // custom message, otherwise we'll keep searching for a valid message.
        if (!is_null($inlineMessage)) {
            return $inlineMessage;
        }
        $customKey = "validation.custom.{$attribute}.{$lowerRule}";
        $customMessage = $customKey;
        // First we check for a custom defined validation message for the attribute
        // and rule. This allows the developer to specify specific messages for
        // only some attributes and rules that need to get specially formed.
        if ($customMessage !== $customKey) {
            return $customMessage;
        }
    }

    public function getMessages() {
        return $this->messages;
    }

    /**
     * Get the inline message for a rule if it exists.
     *
     * @param  string $attribute
     * @param  string $lowerRule
     * @param  array  $source
     *
     * @return string
     */
    protected function getInlineMessage($attribute, $lowerRule, $source = NULL) {
        $source = $source ?: $this->customMessages;
        $keys = array("{$attribute}.{$lowerRule}", $lowerRule);
        // First we will check for a custom message for an attribute specific rule
        // message for the fields, then we will check for a general custom line
        // that is not attribute specific. If we find either we'll return it.
        foreach ($keys as $key) {
            if (isset($source[$key])) {
                return $source[$key];
            }
        }
    }

    /**
     * Get the proper error message for an attribute and size rule.
     *
     * @param  string $attribute
     * @param  string $rule
     *
     * @return string
     */
    protected function getSizeMessage($attribute, $rule) {
        $lowerRule = static::snake_case($rule);
        // There are three different types of size validations. The attribute may be
        // either a number, file, or string so we will check a few things to know
        // which type of value it is and return the correct line for that type.
        $type = $this->getAttributeType($attribute);
        $key = "validation.{$lowerRule}.{$type}";

        return $key;
    }

    /**
     * Get the data type of the given attribute.
     *
     * @param  string $attribute
     *
     * @return string
     */
    protected function getAttributeType($attribute) {
        // We assume that the attributes present in the file array are files so that
        // means that if the attribute does not have a numeric rule and the files
        // list doesn't have it we'll just consider it a string by elimination.
        if ($this->hasRule($attribute, $this->numericRules)) {
            return 'numeric';
        } elseif ($this->hasRule($attribute, array('Array'))) {
            return 'array';
        } elseif (array_key_exists($attribute, $this->files)) {
            return 'file';
        }

        return 'string';
    }

    /**
     * Replace all error message place-holders with actual values.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function doReplacements($message, $attribute, $rule, $parameters) {
        $message = str_replace(':attribute', $this->getAttribute($attribute), $message);
        if (method_exists($this, $replacer = "replace{$rule}")) {
            $message = $this->$replacer($message, $attribute, $rule, $parameters);
        }

        return $message;
    }

    /**
     * Transform an array of attributes to their displayable form.
     *
     * @param  array $values
     *
     * @return array
     */
    protected function getAttributeList(array $values) {
        $attributes = array();
        // For each attribute in the list we will simply get its displayable form as
        // this is convenient when replacing lists of parameters like some of the
        // replacement functions do when formatting out the validation message.
        foreach ($values as $key => $value) {
            $attributes[$key] = $this->getAttribute($value);
        }

        return $attributes;
    }

    /**
     * Get the displayable name of the attribute.
     *
     * @param  string $attribute
     *
     * @return string
     */
    protected function getAttribute($attribute) {
        // The developer may dynamically specify the array of custom attributes
        // on this Validator instance. If the attribute exists in this array
        // it takes precedence over all other ways we can pull attributes.
        if (isset($this->customAttributes[$attribute])) {
            return $this->customAttributes[$attribute];
        }
        $key = "validation.attributes.{$attribute}";
        // If no language line has been specified for the attribute all of the
        // underscores are removed from the attribute name and that will be
        // used as default versions of the attribute's displayable names.
        return str_replace('_', ' ', static::snake_case($attribute));
    }

    /**
     * Get the displayable name of the value.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return string
     */
    public function getDisplayableValue($attribute, $value) {
        if (isset($this->customValues[$attribute][$value])) {
            return $this->customValues[$attribute][$value];
        }

        return $value;
    }

    /**
     * Replace all place-holders for the between rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceBetween($message, $attribute, $rule, $parameters) {
        return str_replace(array(':min', ':max'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the digits rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceDigits($message, $attribute, $rule, $parameters) {
        return str_replace(':digits', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the digits (between) rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceDigitsBetween($message, $attribute, $rule, $parameters) {
        return $this->replaceBetween($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the size rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceSize($message, $attribute, $rule, $parameters) {
        return str_replace(':size', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the min rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceMin($message, $attribute, $rule, $parameters) {
        return str_replace(':min', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the greater than rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceGreaterThan($message, $attribute, $rule, $parameters) {
        return str_replace(':greater_than', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the max rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceMax($message, $attribute, $rule, $parameters) {
        return str_replace(':max', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the in rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceIn($message, $attribute, $rule, $parameters) {
        foreach ($parameters as &$parameter) {
            $parameter = $this->getDisplayableValue($attribute, $parameter);
        }

        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the not_in rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceNotIn($message, $attribute, $rule, $parameters) {
        return $this->replaceIn($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the mimes rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceMimes($message, $attribute, $rule, $parameters) {
        return str_replace(':values', implode(', ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_with rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceRequiredWith($message, $attribute, $rule, $parameters) {
        $parameters = $this->getAttributeList($parameters);

        return str_replace(':values', implode(' / ', $parameters), $message);
    }

    /**
     * Replace all place-holders for the required_with_all rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceRequiredWithAll($message, $attribute, $rule, $parameters) {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_without rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceRequiredWithout($message, $attribute, $rule, $parameters) {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_without_all rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceRequiredWithoutAll($message, $attribute, $rule, $parameters) {
        return $this->replaceRequiredWith($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the required_if rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceRequiredIf($message, $attribute, $rule, $parameters) {
        $parameters[1] = $this->getDisplayableValue($parameters[0], static::array_get($this->data, $parameters[0]));
        $parameters[0] = $this->getAttribute($parameters[0]);

        return str_replace(array(':other', ':value'), $parameters, $message);
    }

    /**
     * Replace all place-holders for the same rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceSame($message, $attribute, $rule, $parameters) {
        return str_replace(':other', $this->getAttribute($parameters[0]), $message);
    }

    /**
     * Replace all place-holders for the different rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceDifferent($message, $attribute, $rule, $parameters) {
        return $this->replaceSame($message, $attribute, $rule, $parameters);
    }

    /**
     * Replace all place-holders for the date_format rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceDateFormat($message, $attribute, $rule, $parameters) {
        return str_replace(':format', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the before rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceBefore($message, $attribute, $rule, $parameters) {
        if (!(strtotime($parameters[0]))) {
            return str_replace(':date', $this->getAttribute($parameters[0]), $message);
        }

        return str_replace(':date', $parameters[0], $message);
    }

    /**
     * Replace all place-holders for the after rule.
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceAfter($message, $attribute, $rule, $parameters) {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Determine if the given attribute has a rule in the given set.
     *
     * @param  string       $attribute
     * @param  string|array $rules
     *
     * @return bool
     */
    protected function hasRule($attribute, $rules) {
        return !is_null($this->getRule($attribute, $rules));
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * @param  string       $attribute
     * @param  string|array $rules
     *
     * @return array|null
     */
    protected function getRule($attribute, $rules) {
        if (!array_key_exists($attribute, $this->rules)) {
            return;
        }
        $rules = (array) $rules;
        foreach ($this->rules[$attribute] as $rule) {
            list($rule, $parameters) = $this->parseRule($rule);
            if (in_array($rule, $rules)) {
                return array($rule, $parameters);
            }
        }
    }

    /**
     * Extract the rule name and parameters from a rule.
     *
     * @param  array|string $rules
     *
     * @return array
     */
    protected function parseRule($rules) {
        if (is_array($rules)) {
            return $this->parseArrayRule($rules);
        }

        return $this->parseStringRule($rules);
    }

    /**
     * Parse an array based rule.
     *
     * @param  array $rules
     *
     * @return array
     */
    protected function parseArrayRule(array $rules) {
        return array(trim(static::array_get($rules, 0)), array_slice($rules, 1));
    }

    /**
     * Parse a string based rule.
     *
     * @param  string $rules
     *
     * @return array
     */
    protected function parseStringRule($rules) {
        $parameters = array();
        // The format for specifying validation rules and parameters follows an
        // easy {rule}:{parameters} formatting convention. For instance the
        // rule "Max:3" states that the value may only be three letters.
        if (strpos($rules, ':') !== FALSE) {
            list($rules, $parameter) = explode(':', $rules, 2);
            $parameters = $this->parseParameters($rules, $parameter);
        }

        return array(trim($rules), $parameters);
    }

    /**
     * Parse a parameter list.
     *
     * @param  string $rule
     * @param  string $parameter
     *
     * @return array
     */
    protected function parseParameters($rule, $parameter) {
        if (strtolower($rule) == 'regex') {
            return array($parameter);
        }

        return str_getcsv($parameter);
    }

    /**
     * Get the array of custom validator extensions.
     *
     * @return array
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Register an array of custom validator extensions.
     *
     * @param  array $extensions
     *
     * @return void
     */
    public function addExtensions(array $extensions) {
        if ($extensions) {
            $keys = array_map('ME_Validator::snake_case', array_keys($extensions));
            $extensions = array_combine($keys, array_values($extensions));
        }
        $this->extensions = array_merge($this->extensions, $extensions);
    }

    /**
     * Register an array of custom implicit validator extensions.
     *
     * @param  array $extensions
     *
     * @return void
     */
    public function addImplicitExtensions(array $extensions) {
        $this->addExtensions($extensions);
        foreach ($extensions as $rule => $extension) {
            $this->implicitRules[] = $rule;
        }
    }

    /**
     * Register a custom validator extension.
     *
     * @param  string          $rule
     * @param  \Closure|string $extension
     *
     * @return void
     */
    public function addExtension($rule, $extension) {
        $this->extensions[static::snake_case($rule)] = $extension;
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param  string          $rule
     * @param  \Closure|string $extension
     *
     * @return void
     */
    public function addImplicitExtension($rule, $extension) {
        $this->addExtension($rule, $extension);
        $this->implicitRules[] = $rule;
    }

    /**
     * Get the array of custom validator message replacers.
     *
     * @return array
     */
    public function getReplacers() {
        return $this->replacers;
    }

    /**
     * Register an array of custom validator message replacers.
     *
     * @param  array $replacers
     *
     * @return void
     */
    public function addReplacers(array $replacers) {
        if ($replacers) {
            $keys = array_map('ME_Validator::snake_case', array_keys($replacers));
            $replacers = array_combine($keys, array_values($replacers));
        }
        $this->replacers = array_merge($this->replacers, $replacers);
    }

    /**
     * Register a custom validator message replacer.
     *
     * @param  string          $rule
     * @param  \Closure|string $replacer
     *
     * @return void
     */
    public function addReplacer($rule, $replacer) {
        $this->replacers[static::snake_case($rule)] = $replacer;
    }

    /**
     * Get the data under validation.
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Set the data under validation.
     *
     * @param  array $data
     *
     * @return void
     */
    public function setData(array $data) {
        $this->data = $this->parseData($data);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * Set the validation rules.
     *
     * @param  array $rules
     *
     * @return $this
     */
    public function setRules(array $rules) {
        $this->rules = $this->explodeRules($rules);

        return $this;
    }

    /**
     * Set the custom attributes on the validator.
     *
     * @param  array $attributes
     *
     * @return $this
     */
    public function setAttributeNames(array $attributes) {
        $this->customAttributes = $attributes;

        return $this;
    }

    /**
     * Set the custom values on the validator.
     *
     * @param  array $values
     *
     * @return $this
     */
    public function setValueNames(array $values) {
        $this->customValues = $values;

        return $this;
    }

    /**
     * Get the files under validation.
     *
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * Set the files under validation.
     *
     * @param  array $files
     *
     * @return $this
     */
    public function setFiles(array $files) {
        $this->files = $files;

        return $this;
    }

    /**
     * Get the custom messages for the validator.
     *
     * @return array
     */
    public function getCustomMessages() {
        return $this->customMessages;
    }

    /**
     * Set the custom messages for the validator.
     *
     * @param  array $messages
     *
     * @return void
     */
    public function setCustomMessages(array $messages) {
        $this->customMessages = array_merge($this->customMessages, $messages);
    }

    /**
     * Get the custom attributes used by the validator.
     *
     * @return array
     */
    public function getCustomAttributes() {
        return $this->customAttributes;
    }

    /**
     * Add custom attributes to the validator.
     *
     * @param  array $customAttributes
     *
     * @return $this
     */
    public function addCustomAttributes(array $customAttributes) {
        $this->customAttributes = array_merge($this->customAttributes, $customAttributes);

        return $this;
    }

    /**
     * Get the custom values for the validator.
     *
     * @return array
     */
    public function getCustomValues() {
        return $this->customValues;
    }

    /**
     * Add the custom values for the validator.
     *
     * @param  array $customValues
     *
     * @return $this
     */
    public function addCustomValues(array $customValues) {
        $this->customValues = array_merge($this->customValues, $customValues);

        return $this;
    }

    /**
     * Get the fallback messages for the validator.
     *
     * @return array
     */
    public function getFallbackMessages() {
        return $this->fallbackMessages;
    }

    /**
     * Set the fallback messages for the validator.
     *
     * @param  array $messages
     *
     * @return void
     */
    public function setFallbackMessages(array $messages) {
        $this->fallbackMessages = $messages;
    }

    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed() {
        return $this->failedRules;
    }

    /**
     * Require a certain number of parameters to be present.
     *
     * @param  int    $count
     * @param  array  $parameters
     * @param  string $rule
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function requireParameterCount($count, $parameters, $rule) {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    /**
     * Helper class, may split to new class
     */
    public static function array_get($array, $key, $default = NULL) {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    public static function snake_case($value, $delimiter = '_') {
        if (!ctype_lower($value)) {
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
        }

        return $value;
    }
}

/**
 * ME Validate
 *
 * Validate data base on rules list
 *
 * @since 1.0
 *
 * @see  ME_Validator
 * @param array  $data Data want to be tested
 *                - attribute : value
 * @param array  $rules The principles of data required
 *                 - attribute : rules list
 * @return boolean
 */
function marketengine_validate($data, $rules, $custom_attributes = array()) {
    $validator = new ME_Validator($data, $rules, $custom_attributes);
    if ($validator->passes()) {
        return true;
    }

    return false;
}
/**
 * Get invalid message
 *
 * Get the list invalid messages based on the rules
 *
 * @since 1.0
 *
 * @see Class ME_Validator
 * @param array  $data Data want to be tested
 *                - attribute : value
 * @param array  $rules The principles of data required
 *                 - attribute : rules list
 * @return array List of invalid field messsage
 *            - attribute : message
 */
function marketengine_get_invalid_message($data, $rules, $custom_attributes = array()) {
    $validator = new ME_Validator($data, $rules, $custom_attributes);
    $validator->invalid();
    return $validator->getMessages();
}