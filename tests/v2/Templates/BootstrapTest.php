<?php

use RBFrameworks\Core\Templates\Bootstrap;

class BootstrapTest extends \PHPUnit\Framework\TestCase
{
    public function testBadge()
    {
        $result = Bootstrap::badge('Test', 'info');
        $this->assertIsString($result);
        $this->assertStringContainsString('badge', $result);
    }

    public function testBadgeWithAttributes()
    {
        $result = Bootstrap::badge('Test', 'success', ['id' => 'my-badge']);
        $this->assertStringContainsString('my-badge', $result);
    }

    public function testTable()
    {
        $array = [
            ['Name' => 'John', 'Age' => 30],
            ['Name' => 'Jane', 'Age' => 25]
        ];
        Bootstrap::table($array);
        $this->assertTrue(true); // Output function, just verify it runs
    }

    public function testFormInput()
    {
        $options = [
            'label' => 'Name',
            'name' => 'name',
            'value' => 'John'
        ];
        Bootstrap::formInput($options);
        $this->assertTrue(true); // Output function
    }

    public function testCheckbox()
    {
        $options = [
            'label' => 'Accept terms',
            'name' => 'terms',
            'value' => '1'
        ];
        Bootstrap::checkbox($options);
        $this->assertTrue(true); // Output function
    }

    public function testRadio()
    {
        $options = [
            'label' => 'Option 1',
            'name' => 'option',
            'value' => '1'
        ];
        Bootstrap::radio($options);
        $this->assertTrue(true); // Output function
    }

    public function testPanel()
    {
        $options = [
            'title' => 'Panel Title',
            'content' => '<p>Panel content</p>'
        ];
        Bootstrap::panel($options);
        $this->assertTrue(true); // Output function
    }

    public function testTemplateAlert()
    {
        $options = [
            'content' => 'Alert message',
            'classe' => 'alert-success'
        ];
        $result = Bootstrap::templateAlert($options);
        $this->assertIsString($result);
    }

    public function testAlert()
    {
        $result = Bootstrap::alert('Error message', 'alert-danger');
        $this->assertIsString($result);
        $this->assertStringContainsString('alert-danger', $result);
    }

    public function testTemplateModal()
    {
        $options = [
            'title' => 'Modal Title',
            'content' => '<p>Modal content</p>'
        ];
        $result = Bootstrap::templateModal($options);
        $this->assertIsString($result);
    }

    public function testTooltip()
    {
        $result = Bootstrap::tooltip('Tooltip text', 'top');
        $this->assertIsString($result);
        $this->assertStringContainsString('tooltip', $result);
    }

    public function testCustomCheckboxs()
    {
        $names = ['option1' => 'Option 1', 'option2' => 'Option 2'];
        Bootstrap::customCheckboxs($names);
        $this->assertTrue(true); // Output function
    }
}
