<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

use SherinBloemendaal\PhpIPAMClient\Exception\PhpIPAMRequestException;

class Subnet extends BaseController
{
    protected static string $controllerName = 'subnets';

    protected int $id;
    protected ?string $subnet;
    protected ?int $mask;
    protected ?string $description;
    protected ?int $sectionId;
    protected ?int $linked_subnet;
    protected ?int $vlanId;
    protected ?int $vrfId;
    protected ?int $masterSubnetId;
    protected ?int $nameserverId;
    protected ?bool $showName;
    protected ?string $permissions;
    protected ?bool $DNSrecursive;
    protected ?bool $DNSrecords;
    protected ?bool $allowRequests;
    protected ?bool $scanAgent;
    protected ?bool $pingSubnet;
    protected ?bool $discoverSubnet;
    protected ?bool $isFolder;
    protected ?bool $isFull;
    protected ?int $state;
    protected ?int $threshold;
    protected ?int $location;
    protected ?string $editDate;

    protected static function transformParamsToIDs(array $params): array
    {
        // sectionId, linked_subnet, vlanId, vrfId, masterSubnetId
        $params = self::getIDFromParams($params, 'sectionId', ['sectionID', 'section'], Section::class);
        $params = self::getIDFromParams($params, 'linked_subnet', ['linked_subnetId'], self::class);
        $params = self::getIDFromParams($params, 'vlanId', ['vlanID', 'vlan'], VLAN::class);

        return self::getIDFromParams($params, 'vrfId', ['vrfID', 'vrf'], VRF::class);
    }

    public static function getAll(): array
    {
        $response = static::_getStatic(['all']);
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $objects = [];

        foreach ($response->getData() as $object) {
            $objects[] = new static($object);
        }

        return $objects;
    }

    public function getUsage(): mixed
    {
        return self::_getStatic([$this->id, 'usage'])->getData();
    }

    public function getFirstFree(): mixed
    {
        return $this->_get([$this->id, 'first_free'])->getData();
    }

    public function getSlaves(): array
    {
        $slaves = $this->_get([$this->id, 'slaves'])->getData();
        $subnets = [];

        foreach ($slaves as $slave) {
            $subnets[] = new self($slave);
        }

        return $subnets;
    }

    public function getSlavesRecursive(): array
    {
        $slaves = $this->_get([$this->id, 'slaves_recursive'])->getData();
        $subnets = [];

        foreach ($slaves as $slave) {
            $subnets[] = new self($slave);
        }

        return $subnets;
    }

    public function getAddresses(): array
    {
        $addresses = $this->_get([$this->id, 'addresses']);
        $addressesArr = [];
        foreach ($addresses->getData() as $address) {
            $addressesArr[] = new Address($address);
        }

        return $addressesArr;
    }

    public function getAddressesIP(string $ip): Address
    {
        $response = $this->_get([$this->id, 'addresses', $ip]);
        if (null === $response->getData()) {
            throw new PhpIPAMRequestException($response);
        }

        $data = array_values($response->getData());

        return new Address($data[0]);
    }

    public function getFirstSubnet(int $mask): mixed
    {
        return $this->_get([$this->id, 'first_subnet', $mask])->getData();
    }

    public function getAllSubnets(int $mask): mixed
    {
        return $this->_get([$this->id, 'all_subnets', $mask])->getData();
    }

    public function getCustomFields(): mixed
    {
        return $this->_get(['custom_fields'])->getData();
    }

    public function getCIDRSearch(string $subnet): mixed
    {
        return $this->_get(['cidr', $subnet])->getData();
    }

    public function getSearch(string $subnet): mixed
    {
        return $this->_get(['search', $subnet])->getData();
    }

    public function postFirstSubnet(int $mask): self
    {
        $response = $this->_post([$this->id, 'first_subnet', $mask]);
        $id = $response->getBody()['id'];

        return self::getByID($id);
    }

    public function patchResize(int $mask): bool
    {
        try {
            $this->_patch([$this->id, 'resize'], ['mask' => $mask]);
        } catch (PhpIPAMRequestException $e) {
            if ('New network is same as old network' === $e->getMessage()) {
                return true;
            }

            throw $e;
        }

        $this->setParams(self::getByID($this->id)->getParams());

        return true;
    }

    public function patchSplit(int $number): bool
    {
        return $this->_patch([$this->id, 'split'], ['number' => $number])->isSuccess();
    }

    public function deleteTruncate(): bool
    {
        return $this->_delete([$this->id, 'truncate'])->isSuccess();
    }

    public function deletePermissions(): bool
    {
        return $this->_delete([$this->id, 'permissions'])->isSuccess();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubnet(): ?string
    {
        return $this->subnet;
    }

    public function getMask(): ?int
    {
        return $this->mask;
    }

    public function setMask(int $mask): self
    {
        $this->mask = $mask;

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

    public function getSectionId(?bool $asObject = null): int|Section|null
    {
        return self::getAsObjectOrID($this->sectionId, Section::class, $asObject);
    }

    public function setSectionId(int|Section|null $sectionId): self
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    public function getLinkedSubnet(?bool $asObject = null): int|self|null
    {
        return self::getAsObjectOrID($this->linked_subnet, self::class, $asObject);
    }

    public function setLinkedSubnet(int|self|null $linked_subnet): self
    {
        $this->linked_subnet = $linked_subnet;

        return $this;
    }

    public function getVlanId(?bool $asObject = null): int|VLAN|null
    {
        return self::getAsObjectOrID($this->vlanId, VLAN::class, $asObject);
    }

    public function setVlanId(int|VLAN|null $vlanId): self
    {
        $this->vlanId = $vlanId;

        return $this;
    }

    public function getVrfId(?bool $asObject = null): int|VRF|null
    {
        return self::getAsObjectOrID($this->vrfId, VRF::class, $asObject);
    }

    public function setVrfId(int|VRF|null $vrfId): self
    {
        $this->vrfId = $vrfId;

        return $this;
    }

    public function getMasterSubnetId(?bool $asObject = null): int|self|null
    {
        return self::getAsObjectOrID($this->masterSubnetId, self::class, $asObject);
    }

    public function setMasterSubnetId(int|self|null $masterSubnetId): self
    {
        $this->masterSubnetId = $masterSubnetId;

        return $this;
    }

    public function getNameserverId(): ?int
    {
        return $this->nameserverId;
    }

    public function setNameserverId(?int $nameserverId): self
    {
        $this->nameserverId = $nameserverId;

        return $this;
    }

    public function getShowName(): ?bool
    {
        return $this->showName;
    }

    public function setShowName(bool $showName): self
    {
        $this->showName = $showName;

        return $this;
    }

    public function getPermissions(): ?string
    {
        return $this->permissions;
    }

    public function setPermissions(string $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getDNSrecursive(): ?bool
    {
        return $this->DNSrecursive;
    }

    public function setDNSrecursive(bool $DNSrecursive): self
    {
        $this->DNSrecursive = $DNSrecursive;

        return $this;
    }

    public function getDNSrecords(): ?bool
    {
        return $this->DNSrecords;
    }

    public function setDNSrecords(bool $DNSrecords): self
    {
        $this->DNSrecords = $DNSrecords;

        return $this;
    }

    public function getAllowRequests(): ?bool
    {
        return $this->allowRequests;
    }

    public function setAllowRequests(bool $allowRequests): self
    {
        $this->allowRequests = $allowRequests;

        return $this;
    }

    public function getScanAgent(): ?bool
    {
        return $this->scanAgent;
    }

    public function setScanAgent(bool $scanAgent): self
    {
        $this->scanAgent = $scanAgent;

        return $this;
    }

    public function getPingSubnet(): ?bool
    {
        return $this->pingSubnet;
    }

    public function setPingSubnet(bool $pingSubnet): self
    {
        $this->pingSubnet = $pingSubnet;

        return $this;
    }

    public function getDiscoverSubnet(): ?bool
    {
        return $this->discoverSubnet;
    }

    public function setDiscoverSubnet(bool $discoverSubnet): self
    {
        $this->discoverSubnet = $discoverSubnet;

        return $this;
    }

    public function getIsFolder(): ?bool
    {
        return $this->isFolder;
    }

    public function setIsFolder(bool $isFolder): self
    {
        $this->isFolder = $isFolder;

        return $this;
    }

    public function getIsFull(): ?bool
    {
        return $this->isFull;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getThreshold(): ?int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function getLocation(): ?int
    {
        return $this->location;
    }

    public function setLocation(int $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }
}
