<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * This form class is responsible to generate the virtual product
 */
class ProductAttachement extends CommonAbstractType
{
    private $translator;
    private $legacyContext;
    private $configuration;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->legacyContext = $legacyContext;
        $this->configuration = $this->getConfiguration();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
            'required' => false,
            'label' => $this->translator->trans('File', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotNull(array('message' => $this->translator->trans('Please select a file', [], 'AdminProducts'))),
                new Assert\File(array('maxSize' => $this->configuration->get('PS_ATTACHMENT_MAXIMUM_SIZE').'M')),
            )
        ))
        ->add('name', 'text', array(
            'label' =>  $this->translator->trans('Filename', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2))
            )
        ))
        ->add('description', 'text', array(
            'label' =>  $this->translator->trans('Description', [], 'AdminProducts')
        ))
        ->add('add', 'button', array(
            'label' =>  $this->translator->trans('Add', [], 'AdminProducts'),
            'attr' =>  ['class' => 'btn-primary pull-right']
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            //if this partial form is submit from a parent form, disable it
            if ($form->getParent()) {
                $event->setData([]);
                $form->add('file', 'file', array('mapped' => false));
                $form->add('name', 'text', array('mapped' => false));
            }
        });
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_attachment';
    }
}
