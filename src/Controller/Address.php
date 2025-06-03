<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

class Address extends BaseController
{
    protected static string $controllerName = 'addresses';

    protected int $id;
    protected int $subnetId;
    protected string $ip;
    protected bool $is_gateway;
    protected ?string $description;
    protected ?string $hostname;
    protected ?string $mac;
    protected ?string $owner;
    protected ?int $tag;
    protected bool $PTRignore;
    protected int $PTR;
    protected ?int $deviceId;
    protected ?string $port;
    protected ?string $note;
    protected ?string $lastSeen;
    protected bool $excludePing;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        $params = self::getIDFromParams($params, 'subnetId', ['subnet', 'subnetID'], Subnet::class);

        return self::getIDFromParams($params, 'deviceId', ['device', 'deviceID'], Device::class);
    }

    public function getPing(): mixed
    {
        return $this->_get([$this->id, 'ping'])->getData();
    }

    public static function getByIPAndSubnet(string $ip, int|Subnet $subnet): self
    {
        if ($subnet instanceof Subnet) {
            $subnet = $subnet->getId();
        }

        return new self(self::_getStatic([$ip, $subnet])->getData());
    }

    public static function getSearchByIP(string $ip): array
    {
        $addr = self::_getStatic(['search', $ip])->getData();
        if (null === $addr || empty($addr)) {
            return [];
        }
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = new self($address);
        }

        return $addresses;
    }

    public static function getSearchByHostname(string $hostname): array
    {
        $addr = self::_getStatic(['search_hostname', $hostname])->getData();
        if (null === $addr || empty($addr)) {
            return [];
        }
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = new self($address);
        }

        return $addresses;
    }

    /**
     * @param int|Section $subnet
     */
    public static function getFirstFree(int|Subnet $subnet): string
    {
        if ($subnet instanceof Subnet) {
            $subnet = $subnet->getId();
        }

        return self::_getStatic(['first_free', $subnet])->getData();
    }

    public static function getCustomFields(): mixed
    {
        return self::_getStatic(['custom_fields'])->getData();
    }

    public static function getTags(): mixed
    {
        return self::_getStatic(['tags'])->getData();
    }

    public static function getTagByID(int $id): mixed
    {
        return self::_getStatic(['tags', $id])->getData();
    }

    public static function getAddressesByTag(int $id): array
    {
        $addr = self::_getStatic(['tags', $id, 'addresses'])->getData();
        if (null === $addr || empty($addr)) {
            return [];
        }
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = new self($address);
        }

        return $addresses;
    }

    public static function postFirstFree(int|Subnet $subnet, array $params = []): self
    {
        if ($subnet instanceof Subnet) {
            $subnet = $subnet->getId();
        }

        $params = self::transformParamsToIDs($params);
        $response = self::_postStatic(['first_free', $subnet], $params);
        $id = $response->getBody()['id'];

        return self::getByID($id);
    }

    public static function deleteByIPAndSubnet(string $ip, int|Subnet $subnet): mixed
    {
        if ($subnet instanceof Subnet) {
            $subnet = $subnet->getId();
        }

        return static::_deleteStatic([$ip, $subnet]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubnetId(?bool $asObject = null): int|Subnet
    {
        return self::getAsObjectOrID($this->subnetId, Subnet::class, $asObject);
    }

    public function setSubnetId(int|Subnet $subnetId): self
    {
        $this->subnetId = $subnetId;

        return $this;
    }

    public function getIP(): string
    {
        return $this->ip;
    }

    public function setIP(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getIsGateway(): bool
    {
        return $this->is_gateway;
    }

    public function setIsGateway(bool $is_gateway): self
    {
        $this->is_gateway = $is_gateway;

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

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(string $mac): self
    {
        $this->mac = $mac;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTag(): ?int
    {
        return $this->tag;
    }

    public function setTag(int $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getPTRignore(): bool
    {
        return $this->PTRignore;
    }

    public function setPTRignore(bool $PTRignore): self
    {
        $this->PTRignore = $PTRignore;

        return $this;
    }

    public function getPTR(): int
    {
        return $this->PTR;
    }

    public function setPTR(int $PTR): self
    {
        $this->PTR = $PTR;

        return $this;
    }

    public function getDeviceID(?bool $asObject = null): int|Device|null
    {
        return self::getAsObjectOrID($this->deviceId, Device::class, $asObject);
    }

    public function setDeviceID(int|Device|null $deviceId): self
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getLastSeen(): ?string
    {
        return $this->lastSeen;
    }

    public function setLastSeen(string $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getExcludePing(): bool
    {
        return $this->excludePing;
    }

    public function setExcludePing(bool $excludePing): self
    {
        $this->excludePing = $excludePing;

        return $this;
    }

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }
}
