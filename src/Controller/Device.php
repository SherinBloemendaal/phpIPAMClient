<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

class Device extends BaseController
{
    protected static string $controllerName = 'devices';

    protected int $id;
    protected ?string $hostname;
    protected ?string $ip;
    protected mixed $type;
    protected ?string $description;
    protected ?array $sections;
    protected ?string $rack;
    protected ?string $rack_start;
    protected ?string $rack_size;
    protected ?string $location;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        if (isset($params['sections'])) {
            $params['sections'] = self::convertSectionsToID($params['sections']);
        }

        return $params;
    }

    public function getSubnets(): array
    {
        $response = $this->_get([$this->getId(), 'subnets']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }

        $subnets = [];

        foreach ($response->getData() as $subnet) {
            $subnets[] = new Subnet($subnet);
        }

        return $subnets;
    }

    public function getAddresses(): array
    {
        $response = $this->_get([$this->getId(), 'addresses']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }

        $addresses = [];

        foreach ($response->getData() as $address) {
            $addresses[] = new Address($address);
        }

        return $addresses;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getIP(): ?string
    {
        return $this->ip;
    }

    public function setIP(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function setType(mixed $type): self
    {
        $this->type = $type;

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

    public function setSections(mixed $sections): self
    {
        $this->sections = $sections;

        return $this;
    }

    public function getRack(): ?string
    {
        return $this->rack;
    }

    public function setRack(string $rack): self
    {
        $this->rack = $rack;

        return $this;
    }

    public function getRackStart(): ?string
    {
        return $this->rack_start;
    }

    public function setRackStart(string $rack_start): self
    {
        $this->rack_start = $rack_start;

        return $this;
    }

    public function getRackSize(): ?string
    {
        return $this->rack_size;
    }

    public function setRackSize(string $rack_size): self
    {
        $this->rack_size = $rack_size;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }
}
