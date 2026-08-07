<?php
namespace Bb\ConsentBanner\Domain\Model;

use \TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


class Component extends AbstractEntity
{
    /**
     * pid
     *
     * @var int | null
     */
    protected ?int $pid = null;

    /**
     * name
     *
     * @var string
     */
    protected string $componentId = '';
    /**
     * name
     *
     * @var string
     */
    protected string $componentHash = '';
    /**
     * integration type (iframe | google_consent_mode | matomo | script)
     *
     * @var string
     */
    protected string $integrationType = 'iframe';
    /**
     * comma-separated google consent mode signals
     *
     * @var string
     */
    protected string $consentModeSignals = '';
    /**
     * name
     *
     * @var string
     */
    protected string $componentTitle = '';
    /**
     * description
     *
     * @var string
     */
    protected string $componentDescription = '';
    /**
     * rejected_script
     *
     * @var string
     */
    protected string $rejectedScript= '';
    /**
     * accepted_script
     *
     * @var string
     */
    protected string $acceptedScript = '';
    /**
     * target
     *
     * @var string
     */
    protected string $moduleTarget = '';
    /**
     * target
     *
     * @var string
     */
    protected string $placeholderTitle = '';
    /**
     * target
     *
     * @var string
     */
    protected string $placeholderDescription = '';
    /**
     * name of the selected service (autocomplete against cookie_database.json)
     *
     * @var string
     */
    protected string $serviceName = '';
    /**
     * comma-separated provider domains of the service
     *
     * @var string
     */
    protected string $serviceProvider = '';
    /**
     * privacy policy link of the service
     *
     * @var string
     */
    protected string $servicePrivacyLink = '';
    /**
     * publisher / legal entity of the service
     *
     * @var string
     */
    protected string $servicePublisher = '';
    /**
     * description of the data collected by the service (current language)
     *
     * @var string
     */
    protected string $serviceDataCollected = '';
    /**
     * cookies belonging to this component (inline children)
     *
     * @var ObjectStorage<Cookie>
     */
    protected ObjectStorage $serviceCookies;

    /**
     * show uri
     *
     * @var string
     */
    protected string $showUri = '';
    /**
     * hidden
     *
     * @var int
     */
    protected int $hidden;
    /**
     * deleted
     *
     * @var int
     */
    protected int $deleted;
    /**
     * __construct
     */
    public function __construct() {
        //Do not remove the next line: It would break the functionality
        $this->serviceCookies = new ObjectStorage();
    }

    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Returns the componentTitle
     *
     * @return string $componentTitle
     */
    public function getComponentTitle(): string
    {
        return $this->componentTitle;
    }

    /**
     * Sets the componentTitle
     *
     * @param string $componentTitle
     * @return void
     */
    public function setComponentTitle(string $componentTitle): void
    {
        $this->componentTitle = $componentTitle;
    }

    /**
     * Returns the rejected Script
     *
     * @return string $rejectedScript
     */
    public function getRejectedScript(): string
    {
        return $this->rejectedScript;
    }

    /**
     * Sets the rejected Script
     *
     * @param string $rejectedScript
     * @return void
     */
    public function setRejectedScript(string $rejectedScript): void
    {
        $this->rejectedScript = $rejectedScript;
    }

    /**
     * Returns the accepted Script
     *
     * @return string $acceptedScript
     */
    public function getAcceptedScript(): string
    {
        return $this->acceptedScript;
    }

    /**
     * Sets the accepted Script
     *
     * @param string $acceptedScript
     * @return void
     */
    public function setAcceptedScript(string $acceptedScript): void
    {
        $this->acceptedScript = $acceptedScript;
    }

    /**
     * Returns the componentDescription
     *
     * @return string $componentDescription
     */
    public function getComponentDescription(): string
    {
        return $this->componentDescription;
    }

    /**
     * Sets the componentDescription
     *
     * @param string $componentDescription
     * @return void
     */
    public function setComponentDescription(string $componentDescription): void
    {
        $this->componentDescription = $componentDescription;
    }

    /**
     * Returns the target
     *
     * @return string $moduleTarget
     */
    public function getModuleTarget(): string
    {
        return $this->moduleTarget;
    }

    /**
     * Sets the target
     *
     * @param string $moduleTarget
     * @return void
     */
    public function setModuleTarget(string $moduleTarget): void
    {
        $this->moduleTarget = $moduleTarget;
    }
    /**
     * Returns the placeholder title
     *
     * @return string $placeholderTitle
     */
    public function getPlaceholderTitle(): string
    {
        return $this->placeholderTitle;
    }

    /**
     * Sets the placeholder title
     *
     * @param string $placeholderTitle
     * @return void
     */
    public function setPlaceholderTitle(string $placeholderTitle): void
    {
        $this->placeholderTitle = $placeholderTitle;
    }
    /**
     * Returns the placeholder
     *
     * @return string $placeholderDescription
     */
    public function getPlaceholderDescription(): string
    {
        return $this->placeholderDescription;
    }

    /**
     * Sets the placeholder
     *
     * @param string $placeholderDescription
     * @return void
     */
    public function setPlaceholderDescription(string $placeholderDescription): void
    {
        $this->placeholderDescription = $placeholderDescription;
    }

    /**
     * Returns the hidden
     *
     * @return int
     */
    public function getHidden():int
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden
     *
     * @param int $hidden
     * @return void
     */
    public function setHidden(int $hidden):void
    {
        $this->hidden = $hidden;
    }
    /**
     * Returns the deleted
     *
     * @return int
     */
    public function getDeleted():int
    {
        return $this->deleted;
    }

    /**
     * Sets the deleted
     *
     * @param int $deleted
     * @return void
     */
    public function setDeleted(int $deleted):void
    {
        $this->deleted = $deleted;
    }
    /**
     * Returns the show Uri
     *
     * @return string $showUri
     */
    public function getShowUri():string
    {

        return $this->showUri;
    }
    /**
     * Sets the show uri
     *
     * @param string $showUri
     * @return void
     */
    public function setShowUri(string $showUri): void
    {
        $this->showUri = $showUri;
    }

    public function getComponentId(): string
    {
        return $this->componentId;
    }

    public function setComponentId(string $componentId): void
    {
        $this->componentId = $componentId;
    }

    public function getComponentHash(): string
    {
        return $this->componentHash;
    }

    public function setComponentHash(string $componentHash): void
    {
        $this->componentHash = $componentHash;
    }

    public function getIntegrationType(): string
    {
        return $this->integrationType;
    }

    public function setIntegrationType(string $integrationType): void
    {
        $this->integrationType = $integrationType;
    }

    /**
     * @return string[] parsed google consent mode signals
     */
    public function getConsentModeSignals(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $this->consentModeSignals))));
    }

    public function setConsentModeSignals(string $consentModeSignals): void
    {
        $this->consentModeSignals = $consentModeSignals;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    public function getServiceProvider(): string
    {
        return $this->serviceProvider;
    }

    public function setServiceProvider(string $serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
    }

    public function getServicePrivacyLink(): string
    {
        return $this->servicePrivacyLink;
    }

    public function setServicePrivacyLink(string $servicePrivacyLink): void
    {
        $this->servicePrivacyLink = $servicePrivacyLink;
    }

    public function getServicePublisher(): string
    {
        return $this->servicePublisher;
    }

    public function setServicePublisher(string $servicePublisher): void
    {
        $this->servicePublisher = $servicePublisher;
    }

    public function getServiceDataCollected(): string
    {
        return $this->serviceDataCollected;
    }

    public function setServiceDataCollected(string $serviceDataCollected): void
    {
        $this->serviceDataCollected = $serviceDataCollected;
    }

    /**
     * @return ObjectStorage<Cookie>
     */
    public function getServiceCookies(): ObjectStorage
    {
        // Extbase reconstitutes objects without invoking the constructor; guard
        // against the typed property being accessed before initialization.
        return $this->serviceCookies ??= new ObjectStorage();
    }

    /**
     * @param ObjectStorage<Cookie> $serviceCookies
     */
    public function setServiceCookies(ObjectStorage $serviceCookies): void
    {
        $this->serviceCookies = $serviceCookies;
    }

    public function addServiceCookie(Cookie $cookie): void
    {
        $this->serviceCookies->attach($cookie);
    }

    public function removeServiceCookie(Cookie $cookie): void
    {
        $this->serviceCookies->detach($cookie);
    }
}
