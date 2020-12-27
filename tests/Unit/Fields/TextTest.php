<?php

declare(strict_types=1);

namespace HazzelForms;

use PHPUnit\Framework\TestCase;
use HazzelForms\HazzelForm;

final class TextTest extends TestCase
{



    private $form;
    private $fieldType = 'text';

    /**
    *  Set up a testing form
    */
    protected function setUp(): void
    {
        $this->form = new HazzelForm();
        $field = $this->form->addField('default', $this->fieldType);
    }



    /**
    *  Test if setup was successful
    */
    public function testSetup(): void
    {
        // test phpunit config
        $this->assertSame('/', $_SERVER['REQUEST_URI']);
        // test setup
        $this->assertInstanceOf(HazzelForm::class, $this->form);
    }



    /**
    *  Test if spaces are trimmed and XSS is prevented
    */
    public function testSetValue(): void
    {
        // given
        $field1 = $this->form->addField('trimmer', $this->fieldType);
        $field2 = $this->form->addField('specialchars', $this->fieldType);
        // when
        $field1->setValue(' Example input ');
        // trimming
        $field2->setValue("<script>alert('test');</script>");
        // illegal chars

        // then
        $this->assertEquals('Example input', $field1->getValue());
        $this->assertEquals("&lt;script&gt;alert('test');&lt;/script&gt;", $field2->getValue());
    }



    /**
    *  Test validation functionality
    */
    public function testValidation(): void
    {
        // given
        $field1 = $this->form->addField('required empty field', $this->fieldType);
        $field2 = $this->form->addField('required space filled field', $this->fieldType);
        $field3 = $this->form->addField('required non empyt field', $this->fieldType);
        $field4 = $this->form->addField('non required empty field', $this->fieldType, ['required' => false]);
        // when
        $field1->setValue('');
        $field2->setValue(' ');
        // will be trimmed
        $field3->setValue('content');
        $field4->setValue('');
        // then
        $this->assertFalse($field1->validate());
        // TODO replace function name with isValid()
        $this->assertFalse($field2->validate());
        $this->assertTrue($field3->validate());
        $this->assertTrue($field4->validate());
    }
}
