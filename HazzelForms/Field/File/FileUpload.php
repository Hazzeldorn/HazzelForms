<?php

namespace HazzelForms;

class FileUpload extends Field {

    protected $maxsize,
              $maxfiles,
              $multiple,
              $label,
              $types,
              $limitSearch,
              $fieldValue = array(); // override string initialization

    private $mimeTypes = array(
        // images
        'gif' => array('image/gif', 'image/gi_'),
        'png' => array('image/png', 'application/png', 'application/x-png'),
        'jpg' => array('image/jp_', 'application/jpg', 'application/x-jpg', 'image/pjpeg', 'image/jpeg'),

        // documents
        'txt' => array('application/plain', 'text/plain'),
        'pdf' => array('application/pdf', 'application/x-pdf', 'application/acrobat', 'text/pdf', 'text/x-pdf'),

        // archives
        'zip' => array('application/gzip', 'application/x-gzip', 'application/x-gunzip', 'application/gzipped', 'application/x-compressed', 'application/gzip-compressed', 'gzip/document', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
        'tar' => array('application/tar', 'application/x-tar', 'applicaton/x-gtar', 'multipart/x-tar')
    );



    public function __construct($fieldName, $formName, $args = array()) {
        parent::__construct($fieldName, $formName, $args);

        $this->fieldType = 'file';
        $this->maxsize      = (isset($args['maxsize']))      ? $args['maxsize']      : 16;
        $this->maxfiles     = (isset($args['maxfiles']))     ? $args['maxfiles']     : 10;
        $this->types        = (isset($args['types']))        ? $args['types']        : array_keys($this->mimeTypes); // allow all defined mime types
        $this->limitSearch  = (isset($args['limit_search'])) ? $args['limit_search'] : true; // only show accepted files in file explorer
        $this->multiple     = (isset($args['multiple']))     ? $args['multiple']     : false;
        $this->label        = (isset($args['label']))        ? $args['label']        : '';
    }

    protected function buildAttributeString() {
      $attributes = '';

      if($this->multiple){
        $attributes .= ' multiple';
      }
      if($this->limitSearch){
        $attributes .= ' accept="';
        foreach ($this->types as $type) {
          $attributes .= '.'.$type.',';
        } unset($type);
        $attributes = rtrim($attributes, ',').'"'; // remove last point
      }
      if($this->required){
        $attributes .= ' required';
      }

      return $attributes;
    }

    public function returnField()   {
        return sprintf('<input type="%1$s" name="%2$s[%3$s][]" class="%4$s" %5$s />', $this->fieldType, $this->formName, $this->fieldSlug, $this->classlist, $this->buildAttributeString());
    }

    public function validate() {

        if( isset($_FILES[$this->formName]['name'][$this->fieldSlug])
            && !empty($_FILES[$this->formName]['name'][$this->fieldSlug]) ) {

          // resort the stupid structured $_FILES array
          $fileNames  = $_FILES[$this->formName]['name'][$this->fieldSlug];
          $fileTemp   = $_FILES[$this->formName]['tmp_name'][$this->fieldSlug];
          $fileErrors = $_FILES[$this->formName]['error'][$this->fieldSlug];
          $fileSizes  = $_FILES[$this->formName]['size'][$this->fieldSlug];

          if(count($fileNames) > $this->maxfiles){
            $this->error = 'too_many';
          } else {
            for($i = 0; $i < count($fileNames); $i++) {

              if(empty($fileErrors[$i]) && empty($this->error)){
                // upload for this file was successfull

                // check if filename contains forbidden characters
                if(!preg_match('/^[\p{L}\s\d\-~_,;\[\]\(\)\']{1,200}\.[a-zA-Z0-9]{1,10}$/', $fileNames[$i])){
                  $this->error = 'invalid_filename';
                  break;
                }

                // check if filesize is ok
                if($fileSizes[$i] > ($this->maxsize * 1000000)){
                  $this->error = 'too_big';
                  break;
                }

                // mime type validaton
                $typeValid = false;
                foreach($this->types as $type){
                  // for each type that is defined as accepted: check if file matches
                  if( in_array( mime_content_type($fileTemp[$i]), $this->mimeTypes[$type] ) ){
                    $typeValid = true;
                    break;
                  }
                } unset($type);

                  if(!$typeValid){
                    $this->error = 'invalid';
                    break;
                  } else {
                    // file is ok -> assign field value
                    array_push($this->fieldValue, array(
                        'dir'  => $fileTemp[$i],
                        'name' => $fileNames[$i]
                      ));
                  }

                } else {
                  // file broken during upload
                  $this->error = 'invalid';
                  break;
                }

              } // endfor
          } // end else

        } else {
          // no file was uploaded
          if ($this->required){
              $this->error = 'empty';
          }
        }

        $this->validated = true;
        return $this->isValid();
    }
}
