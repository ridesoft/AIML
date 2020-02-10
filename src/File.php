<?php

/*
 *
 * (c) Maurizio Brioschi <maurizio.brioschi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ridesoft\AIML;

use Ridesoft\AIML\Exception\InvalidCategoryException;

class File
{
    /** @var string */
    protected $aimlFile;
    /** @var array|Category */
    protected $categories = [];

    /**
     * @param string $aimlFile
     * @return File
     */
    public function setAimlFile(string $aimlFile): self
    {
        $this->aimlFile = $aimlFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getAimlFile(): ?string
    {
        return $this->aimlFile;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        if ($this->getAimlFile() === null) {
            throw new \Exception('Aiml file not set');
        }
        if (empty($this->categories)) {
            $this->parse();
        }
        return $this->categories;
    }

    /**
     * @return $this
     */
    protected function parse(): self
    {
        $xmldata = simplexml_load_file($this->getAimlFile());
        if (!$xmldata) {
            throw new \InvalidArgumentException(
                'File ' . $this->getAimlFile() . ' is not a valid xml(aiml) file'
            );
        }
        for ($i = 0; $i < count($xmldata->category); $i++) {
            if (!property_exists($xmldata->category[$i], 'pattern')) {
                throw new InvalidCategoryException('Pattern not found in file ' . $this->getAimlFile());
            }
            if (!property_exists($xmldata->category[$i], 'template')) {
                throw new InvalidCategoryException('Template not found in file ' . $this->getAimlFile());
            }
            array_push(
                $this->categories,
                new Category($xmldata->category[$i]->pattern, $xmldata->category[$i]->template)
            );
        }

        return $this;
    }
}
