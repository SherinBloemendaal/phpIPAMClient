<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

class VRF extends BaseController
{
    protected static string $controllerName = 'vrf';

    protected int $id;
    protected ?string $name;
    protected ?string $rd;
    protected ?string $description;
    protected ?array $sections;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        if (isset($params['sections'])) {
            $params['sections'] = self::convertSectionsToID($params['sections']);
        }

        return $params;
    }

    public static function getAll(): array
    {
        $response = self::_getStatic();
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $vrfs = [];

        foreach ($response->getData() as $vrf) {
            $vrfs[] = new self($vrf);
        }

        return $vrfs;
    }

    public function getSubnets(): array
    {
        $response = $this->_get([$this->id, 'subnets']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $subnets = [];

        foreach ($response->getData() as $subnet) {
            $subnets[] = new Subnet($subnet);
        }

        return $subnets;
    }

    public static function getCustomFields(): mixed
    {
        return self::_getStatic(['custom_fields'])->getData();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRd(): ?string
    {
        return $this->rd;
    }

    public function setRd(string $rd): self
    {
        $this->rd = $rd;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSections(?bool $asObject = null): array
    {
        if (null === $this->sections) {
            return [];
        }

        $sections = [];
        foreach ($this->sections as $section) {
            $sections[] = self::getAsObjectOrID($section, Section::class, $asObject);
        }
        $this->sections = $sections;

        return $this->sections;
    }

    public function setSections(array $sections): self
    {
        $this->sections = $sections;

        return $this;
    }

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }
}
