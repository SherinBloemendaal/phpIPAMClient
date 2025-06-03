<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

use SherinBloemendaal\PhpIPAMClient\Exception\PhpIPAMException;

/**
 * Class Section.
 */
class Section extends BaseController
{
    /**
     * Name of the controller.
     */
    protected static string $controllerName = 'sections';

    /**
     * Section identifier, identifies which section to work on.
     */
    protected int $id;
    /**
     * Section name, unique value.
     */
    protected ?string $name;
    /**
     * Description of section.
     */
    protected ?string $description;
    /**
     * Id of master section if section is nested (default: 0).
     */
    protected ?int $masterSection;
    /**
     * Json encoded group permissions for section groupId:permission_level (0-3).
     */
    protected ?string $permissions;
    /**
     * Weather to check consistency for subnets and IP addresses (default: 0).
     */
    protected ?bool $strictMode;
    /**
     * Order of subnets in this section (default: subnet,asc).
     */
    protected ?string $subnetOrdering;
    /**
     * Order of sections list display.
     */
    protected ?int $order;
    /**
     * Date of last edit (yyyy-mm-dd hh:ii:ss).
     */
    protected ?string $editDate;
    /**
     * Show / hide VLANs in subnet list (default: 0).
     */
    protected ?bool $showVLAN;
    /**
     * Show / hide VRFs in subnet list(default: 0).
     */
    protected ?bool $showVRF;
    /**
     * Show only supernets in subnet list(default: 0).
     */
    protected ?bool $showSupernetOnly;
    /**
     * Id of NS resolver to be used for section.
     */
    protected ?string $DNS;

    protected static function transformParamsToIDs(array $params): array
    {
        return self::getIDFromParams($params, 'masterSection', ['masterSectionId', 'masterSectionID'], self::class);
    }

    /**
     * Returns all subnets in section.
     */
    public function getAllSubnets(): array
    {
        $response = $this->_get([$this->id, 'subnets']);
        $subnets = [];
        foreach ($response->getData() as $subnet) {
            $subnets[] = new Subnet($subnet);
        }

        return $subnets;
    }

    /**
     * Returns specific section by name.
     */
    public static function getByName(string $name): self
    {
        $response = self::_getStatic([$name]);

        return new self($response->getData());
    }

    /**
     * Returns custom section fields
     * Note: this will throw an exception.
     */
    public static function getCustomFields(): mixed
    {
        $response = self::_getStatic(['custom_fields']);

        return $response->getData();
    }

    /**
     * Creates a new Section.
     *
     * @throws PhpIPAMException
     */
    public static function post(array $params): self
    {
        // Check if there is at all a name given
        if (\array_key_exists('name', $params)) {
            $params = self::transformParamsToIDs($params);
            self::_postStatic([], $params);
        } else {
            throw new PhpIPAMException('Name is not given. Provide at least a name for the section.');
        }

        // Section is created lets get it
        return self::getByName($params['name']);
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

    public function getMasterSection(?bool $asObject = null): int|self|null
    {
        return self::getAsObjectOrID($this->masterSection, self::class, $asObject);
    }

    public function setMasterSection(int|self|null $masterSection): self
    {
        $this->masterSection = $masterSection;

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

    public function getStrictMode(): ?bool
    {
        return $this->strictMode;
    }

    public function setStrictMode(bool $strictMode): self
    {
        $this->strictMode = $strictMode;

        return $this;
    }

    public function getSubnetOrdering(): ?string
    {
        return $this->subnetOrdering;
    }

    public function setSubnetOrdering(string $subnetOrdering): self
    {
        $this->subnetOrdering = $subnetOrdering;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getEditDate(): ?string
    {
        return $this->editDate;
    }

    public function getShowVLAN(): ?bool
    {
        return $this->showVLAN;
    }

    public function setShowVLAN(bool $showVLAN): self
    {
        $this->showVLAN = $showVLAN;

        return $this;
    }

    public function getShowVRF(): ?bool
    {
        return $this->showVRF;
    }

    public function setShowVRF(bool $showVRF): self
    {
        $this->showVRF = $showVRF;

        return $this;
    }

    public function getShowSupernetOnly(): ?bool
    {
        return $this->showSupernetOnly;
    }

    public function setShowSupernetOnly(bool $showSupernetOnly): self
    {
        $this->showSupernetOnly = $showSupernetOnly;

        return $this;
    }

    public function getDNS(): ?string
    {
        return $this->DNS;
    }

    public function setDNS(string $DNS): self
    {
        $this->DNS = $DNS;

        return $this;
    }
}
