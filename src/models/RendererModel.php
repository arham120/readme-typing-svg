<?php declare (strict_types = 1);

/**
 * Model for Typing SVG parameters
 */
class RendererModel
{
    /** @var array<string> $lines text to display */
    public $lines;

    /** @var string $font font family */
    public $font;

    /** @var string $color font color */
    public $color;

    /** @var int $size font size */
    public $size;

    /** @var bool $center whether or not to center text */
    public $center;

    /** @var int $width SVG width (px) */
    public $width;

    /** @var int $height SVG height (px) */
    public $height;

    /** @var array<string, string> $DEFAULTS */
    private $DEFAULTS = array(
        "font" => "JetBrains Mono",
        "color" => "#36BCF7",
        "size" => "20",
        "center" => "false",
        "width" => "400",
        "height" => "50",
    );

    public function __construct()
    {
        $this->lines = $this->checkLines($_REQUEST["lines"]);
        $this->font = $this->checkFont($_REQUEST["font"] ?? $this->DEFAULTS["font"]);
        $this->color = $this->checkColor($_REQUEST["color"] ?? $this->DEFAULTS["color"]);
        $this->size = $this->checkNumber($_REQUEST["size"] ?? $this->DEFAULTS["size"], "Font size");
        $this->center = $this->checkCenter($_REQUEST["center"] ?? $this->DEFAULTS["center"]);
        $this->width = $this->checkNumber($_REQUEST["width"] ?? $this->DEFAULTS["width"], "Width");
        $this->height = $this->checkNumber($_REQUEST["height"] ?? $this->DEFAULTS["height"], "Height");
        $this->template = "templates/svg.php";
    }

    /**
     * Validate lines and return array of string
     *
     * @param string|NULL $lines - semicolon separated lines parameter
     * @return array<string>
     */
    private function checkLines($lines)
    {
        if (!isset($lines)) {
            throw new InvalidArgumentException("Lines parameter must be set.");
        }
        $exploded = explode(";", $lines);
        if (!$exploded) {
            throw new InvalidArgumentException("Lines parameter is invalid.");
        }
        return $exploded;
    }

    /**
     * Validate font family and return valid string
     *
     * @param string $font - font name parameter
     * @return string
     */
    private function checkFont($font)
    {
        // return escaped font name
        return (string) preg_replace("/[^0-9A-Za-z+'\-()!&*_ ]/", "", $font);
    }

    /**
     * Validate font color and return valid string
     *
     * @param string|NULL $color - color parameter
     * @return string
     */
    private function checkColor($color)
    {
        $escaped = (string) preg_replace("/[^0-9A-Fa-f]/", "", $color);
        // if color is not a valid length, use the default
        if (!in_array(strlen($escaped), [3, 4, 6, 8])) {
            return $this->DEFAULTS["color"];
        }
        // return escaped color
        return "#" . $escaped;
    }

    /**
     * Validate numeric parameter and return valid integer
     *
     * @param string|NULL $num - parameter to validate
     * @param string $field - field name for displaying in case of error
     * @return int
     */
    private function checkNumber($num, $field)
    {
        $digits = intval(preg_replace("/[^0-9\-]/", "", $num));
        if ($digits <= 0) {
            throw new InvalidArgumentException("$field must be a positive number.");
        }
        return $digits;
    }

    /**
     * Validate center alignment and return boolean
     *
     * @param string|NULL $center - center parameter
     * @return boolean
     */
    private function checkCenter($center)
    {
        return isset($center) ? ($center == "true") : $this->DEFAULTS["center"];
    }
}