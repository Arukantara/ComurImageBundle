<?php

namespace Comur\ImageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CroppableGalleryType extends CroppableImageType
{
    protected $galleryDir = null;
    protected $thumbsDir = null;
    protected $isGallery = true;
    protected $galleryThumbSize = null;

    // public function getParent()
    // {
    //     return 'collection';
    // }

    public function getName()
    {
        return 'comur_gallery';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // if($options['uploadConfig']['saveOriginal']){
        //     $form->getParent()->add($options['uploadConfig']['saveOriginal'], 'hidden');
        // }
        // var_dump($builder->getDataMapper());exit;
        if($options['uploadConfig']['saveOriginal']){
            $builder->add($options['uploadConfig']['saveOriginal'], 'collection', array(
                // 'inherit_data' => true,
                // 'property_path' => $options['uploadConfig']['saveOriginal'],
                'entry_type' => 'hidden',
                'allow_add' => function(Options $options, $value){ return true; },
                'allow_delete' => function(Options $options, $value){ return true; }
            ));
        }

        $builder->add($builder->getName(), 'collection', array(
            // 'property_path' => $builder->getName(),
            // 'inherit_data' => true,
            'entry_type' => 'hidden',
            'allow_add' => function(Options $options, $value){ return true; },
            'allow_delete' => function(Options $options, $value){ return true; }
        ));
    }

    public function __construct($galleryDir, $thumbsDir, $galleryThumbSize)
    {
        $this->galleryDir = $galleryDir;
        $this->thumbsDir = $thumbsDir;
        $this->galleryThumbSize = $galleryThumbSize;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {


        parent::setDefaultOptions($resolver);

        $galleryDir = $this->galleryDir;

        // $resolver->setNormalizers(array(
            
            
        // ));
         $uploadConfig = array(
             'uploadRoute' => 'comur_api_upload',
             'uploadUrl' => $galleryDir,
             'webDir' => null,
             'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',
             'libraryDir' => null,
             'libraryRoute' => 'comur_api_image_library',
             'showLibrary' => true,
             'saveOriginal' => 'originalGallery'
         );

         $cropConfig = array(
             'minWidth' => 1,
             'minHeight' => 1,
             'aspectRatio' => true,
             'cropRoute' => 'comur_api_crop',
             'forceResize' => false,
             'thumbs' => null
         );

         $resolver->setDefaults(array(
             'uploadConfig' => $uploadConfig,
             'cropConfig' => $cropConfig,
         ));
        
         $resolver->setNormalizers(array(
             'uploadConfig' => function(Options $options, $value) use ($uploadConfig){
                 $config = array_merge($uploadConfig, $value);
                 if(!isset($config['libraryDir'])){
                     $config['libraryDir'] = $config['uploadUrl'];
                 }
                 return $config;
             },
             'cropConfig' => function(Options $options, $value) use($cropConfig){
                 return array_merge($cropConfig, $value);
             }
         ));
        
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $uploadConfig = $options['uploadConfig'];
        $cropConfig = $options['cropConfig'];
        // $options['type'] = 'text';

        // var_dump($options);exit;

        $uploadConfig['isGallery'] = true;
        $view->vars['options'] = array('uploadConfig' => $uploadConfig, 'cropConfig' => $cropConfig, 'galleryThumbSize' => $this->galleryThumbSize);
        // $view->vars['options']['attr'] = array('style' => 'opacity: 0;width: 0; max-width: 0; height: 0; max-height: 0;', 'multiple' => true);
        // $view->vars['attr'] = array('style' => 'opacity: 0;width: 0; max-width: 0; height: 0; max-height: 0;', 'multiple' => true);
    }
}