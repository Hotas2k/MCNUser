<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Options\Authentication\Plugin;

/**
 * Class Standard
 * @package MCNUser\Options\Authentication\Plugin
 */
class Standard extends AbstractPluginOptions
{
    /**
     * @var string
     */
    protected $entity_identity_property = 'email';

    /**
     * @var string
     */
    protected $entity_credential_property = 'password';

    /**
     * @var string
     */
    protected $http_identity_field = 'identity';

    /**
     * @var string
     */
    protected $http_credential_field = 'credential';

    /**
     * A callable method that is applied before comparing passwords
     *
     * @var \Callable
     */
    protected $credential_treatment = 'sha1';

    /**
     * Class name of representing plugin
     *
     * @return string
     */
    public function getClassName()
    {
        return 'MCNUser\Authentication\Plugin\Standard';
    }

    /**
     * @param Callable $credential_treatment
     */
    public function setCredentialTreatment(Callable $credential_treatment)
    {
        $this->credential_treatment = $credential_treatment;
    }

    /**
     * @return Callable
     */
    public function getCredentialTreatment()
    {
        return $this->credential_treatment;
    }

    /**
     * @param string $entity_credential_property
     */
    public function setEntityCredentialProperty($entity_credential_property)
    {
        $this->entity_credential_property = $entity_credential_property;
    }

    /**
     * @return string
     */
    public function getEntityCredentialProperty()
    {
        return $this->entity_credential_property;
    }

    /**
     * @param string $entity_identity_property
     */
    public function setEntityIdentityProperty($entity_identity_property)
    {
        $this->entity_identity_property = $entity_identity_property;
    }

    /**
     * @return string
     */
    public function getEntityIdentityProperty()
    {
        return $this->entity_identity_property;
    }

    /**
     * @param string $http_credential_field
     */
    public function setHttpCredentialField($http_credential_field)
    {
        $this->http_credential_field = $http_credential_field;
    }

    /**
     * @return string
     */
    public function getHttpCredentialField()
    {
        return $this->http_credential_field;
    }

    /**
     * @param string $http_identity_field
     */
    public function setHttpIdentityField($http_identity_field)
    {
        $this->http_identity_field = $http_identity_field;
    }

    /**
     * @return string
     */
    public function getHttpIdentityField()
    {
        return $this->http_identity_field;
    }
}
