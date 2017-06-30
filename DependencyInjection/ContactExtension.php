<?php

namespace VZenix\Bundle\ContactBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VZenix\Bundle\ContactBundle\DependencyInjection\Configuration;

/**
 * Extension structure for contact bundle
 * @author Francisco Muros Espadas <paco@vzenix.es>
 */
class ContactExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $lConfigs, ContainerBuilder $iContainer)
    {
        $_iConfiguration = new Configuration();
        $_iProcessedConfig = $this->processConfiguration($_iConfiguration, $lConfigs);

        $_sMailSubject = isset($_iProcessedConfig['mail']) &&
                isset($_iProcessedConfig['mail']['subject']) ?
                $_iProcessedConfig['mail']['subject'] : null;
        $iContainer->setParameter('vzenix.contact.mail.subject', $_sMailSubject);

        $_sMailTo = isset($_iProcessedConfig['mail']) &&
                isset($_iProcessedConfig['mail']['to']) ?
                $_iProcessedConfig['mail']['to'] : null;
        $iContainer->setParameter('vzenix.contact.mail.to', $_sMailTo);

        $_sMailFrom = isset($_iProcessedConfig['mail']) &&
                isset($_iProcessedConfig['mail']['from']) ?
                $_iProcessedConfig['mail']['from'] : null;
        $iContainer->setParameter('vzenix.contact.mail.from', $_sMailFrom);

        $_sTemplateMails = isset($_iProcessedConfig['templates']) &&
                isset($_iProcessedConfig['templates']['mails']) ?
                $_iProcessedConfig['templates']['mails'] : null;
        $iContainer->setParameter('vzenix.contact.templates.mailer', $_sTemplateMails);

        $_sTemplateView = isset($_iProcessedConfig['templates']) &&
                isset($_iProcessedConfig['templates']['view']) ?
                $_iProcessedConfig['templates']['view'] : 'default/contact.html.twig';
        $iContainer->setParameter('vzenix.contact.templates.view', $_sTemplateView);

        $_bLog = isset($_iProcessedConfig['log']) &&
                $_iProcessedConfig['log'] === true ? true : false;
        $iContainer->setParameter('vzenix.contact.log', $_bLog);

        $_bLapsus = isset($_iProcessedConfig['lapsus']) ? 
                (int) ($_iProcessedConfig['lapsus']) : 2;
        $iContainer->setParameter('vzenix.contact.lapsus', $_bLapsus);
        
        

        $_bSwiftmailer = isset($_iProcessedConfig['swiftmailer']) &&
                is_string($_sTemplateMails) &&
                is_string($_sMailSubject) &&
                is_string($_sMailTo) &&
                is_string($_sMailFrom) &&
                $_iProcessedConfig['swiftmailer'] === true ? true : false;
        $iContainer->setParameter('vzenix.contact.swiftmailer', $_bSwiftmailer);
    }

}
