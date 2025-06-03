<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

class VLAN extends BaseController
{
    protected static string $controllerName = 'vlan';

    protected int $vlanId;
    protected ?int $domainId;
    protected ?string $name;
    protected ?int $number;
    protected ?string $description;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        return self::getIDFromParams($params, 'domainId', ['domainID', 'domain'], L2Domain::class);
    }

    public function getSubnets(): array
    {
        $response = $this->_get([$this->vlanId, 'subnets']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }

        $subnets = [];

        foreach ($response->getData() as $subnet) {
            $subnets[] = new Subnet($subnet);
        }

        return $subnets;
    }

    public function getSubnetsInSection(int|Section $section): array
    {
        if ($section instanceof Section) {
            $section = $section->getId();
        }

        $response = $this->_get([$this->vlanId, 'subnets', $section]);

        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }

        $subnets = [];

        foreach ($response->getData() as $subnet) {
            $subnets[] = new Subnet($subnet);
        }

        return $subnets;
    }

    public function getCustomFields(): mixed
    {
        return $this->_get([$this->vlanId, 'custom_fields'])->getData();
    }

    public function getSearch(int $number): array
    {
        $response = $this->_get([$this->vlanId, 'search', $number]);

        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }

        $vlans = [];

        foreach ($response->getData() as $vlan) {
            $vlans[] = new self($vlan);
        }

        return $vlans;
    }

    public function delete(): bool
    {
        return $this->_delete([], ['vlanId' => $this->getId()])->isSuccess();
    }

    public function getId(): int
    {
        return $this->vlanId;
    }

    public function getDomainId(?bool $asObject = null): int|L2Domain|null
    {
        return self::getAsObjectOrID($this->domainId, L2Domain::class, $asObject);
    }

    public function setDomainId(int|L2Domain|null $domainId): self
    {
        $this->domainId = $domainId;

        return $this;
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

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

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

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }
}
