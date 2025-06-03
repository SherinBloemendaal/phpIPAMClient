<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

class L2Domain extends BaseController
{
    protected static string $controllerName = 'l2domains';

    protected int $id;
    protected ?string $name;
    protected ?string $description;
    protected ?array $sections;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        return $params;
    }

    public static function getAll(): array
    {
        $response = self::_getStatic();
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $domains = [];

        foreach ($response->getData() as $domain) {
            $domains[] = new self($domain);
        }

        return $domains;
    }

    public function getVLANs(): array
    {
        $response = $this->_get([$this->id, 'vlans']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $vlans = [];

        foreach ($response->getData() as $vlan) {
            $vlans[] = new VLAN($vlan);
        }

        return $vlans;
    }

    public function patch(array $params = []): bool
    {
        $this->setParams($params);
        $params = $this->getParams();

        return $this->_patch([], $params)->isSuccess();
    }

    public function delete(): bool
    {
        return $this->_delete([$this->id])->isSuccess();
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
