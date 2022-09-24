<?php

namespace Core\Templates;

class Bootstrap
{
    /*
Última Atualização em 16/11/2016
Syncs:
	midiacriativa.com/v2
	frutamil.com.br/v3
	eduardopaixao.com.br/v3

	Bootstrap::templateModal(array('content' => '', 'title' => ''));
	Bootstrap::templateAlert(array('content' => '', 'title' => ''));
	Bootstrap::toltip($text, $placement);
	Bootstrap::panel();
*/
	
    public static function badge(string $text, string $class = 'info', $attributes = []): string {
        $strAttributes = "";
        foreach($attributes as $attribute => $value) {
            $strAttributes.= "$attribute=\"$value\" ";
        }
        return " <span {$strAttributes} class=\"badge badge-{$class}\">{$text}</span> ";
    }




    static public function table($array) {
		echo '<table class="table">';
		foreach($array as $i => $row ) {
			
			echo '<tr>';
			foreach($row as $chave => $valor) {
				echo "<th>$chave</th>";
			}
			echo '</tr>';
			
			
			
			echo '<tr>';
			
			foreach($row as $chave => $valor) {
			
				echo "<td>$valor</td>";
			
			}
			
			echo '</tr>';
			
		}
		echo '</table>';
	}
	
	
	
	
	
	
	
	
	
	
	/*
	Função de Formulário Incluída em 03/03/2017
	*/
	static public function formInput($options = null) {
		$optionsDefault = array(
			'id'		=>	'formInput'.rand(0,999)*rand(0,999)	
		,	'label'	=>	'Nome do Campo'
		,	'labelSize'	=>	3
		,	'inputSize'	=>	9
		,	'name'			=>	'campo'
		,	'class'			=>	''
		,	'placeholder'	=>	''
		,	'value'			=>	''
		,	'type'			=>	'text'
		,	'required'		=>	false
		);		
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		$options['required'] = ($options['required']) ? 'required=""' : '';
		ob_start();
		?>
		<!--// Campo //-->
		<div class="form-group">
			<label class="col-xs-{labelSize} col-sm-{labelSize} col-md-{labelSize} col-lg-{labelSize} control-label">{label}</label>
			<div class="col-xs-{inputSize} col-sm-{inputSize} col-md-{inputSize} col-lg-{inputSize}">
				<input name="{name}" class="form-control {class}" placeholder="{placeholder}" {required} value="{value}" type="{type}" />
			</div>
		</div>
		<?php return smart_replace(null, $options, true);
	}
	
	
	static function customCheckboxs($names) {
		foreach($names as $name => $label) {
			echo "<label class=\"navbar-link\"><input ng-model=\"$name\" ng-change=\"setFilter('$name', $name)\" type=\"checkbox\" /> $label</label>";
		}
	}
	
	static function checkbox($options = null) {
		$optionsDefault = array(
			'id'		=>	'radio'.rand(0,999)*rand(0,999)	
		,	'checked'	=>	''
		,	'name'	=>	''
		,	'label'		=>	''
		,	'value'		=>	''
		,	'class'		=>	''
		);		
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		ob_start();
		?>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="{name}" id="{id}" value="{value}" {checked} class="{class}" />
				{label}
			</label>
		</div>	
		<?php return smart_replace(null, $options, true);	
		
	}	
	
	static function radio($options = null) {
		$optionsDefault = array(
			'id'		=>	'radio'.rand(0,999)*rand(0,999)	
		,	'checked'	=>	''
		,	'name'	=>	''
		,	'label'		=>	''
		,	'value'		=>	''
		,	'class'		=>	''
		);		
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		ob_start();
		?>
		<div class="radio">
			<label>
				<input type="radio" name="{name}" id="{id}" value="{value}" {checked} class="{class}" />
				{label}
			</label>
		</div>	
		<?php return smart_replace(null, $options, true);	
		
	}
	
	static function panel($options = null) {
		$optionsDefault = array(
			'id'			=>	'panel'.rand(0,999)*rand(0,999)	
		,	'title'			=>	''
		,	'content'		=>	'<p>use content para Conteudo de Texto</p>'
		,	'footer'		=>	''
		,	'class'			=>	'panel-default' //panel-primary panel-success panel-info panel-warning panel-danger
		,	'extracontent'	=>	''
		);
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		ob_start();
		?>
		<div class="panel {class}">
			<?php if(!empty($options['title'])) { ?>
			<div class="panel-heading">
				<h3 class="panel-title">{title}</h3>
			</div>
			<?php } ?>
			<div class="panel-body">
				{content}
			</div>
			{extracontent}
			<?php if(!empty($options['footer'])) { ?>
			<div class="panel-footer">{footer}</div>
			<?php } ?>
		</div>
		<?php 
		return smart_replace(null, $options, true);
	}
	
	/*
	static function templateModal($options = null) {
		if(is_null($options)) $options = array(
			'id'			=>	'modal'.rand(0,999)*rand(0,999)	
		,	'buttonName'	=>	'buttonName'
		,	'title'			=>	'buttonName'
		,	'content'		=>	'<p>use content para Conteudo de Texto</p>'
		,	'buttonClose'	=>	'Fechar'
		);
		ob_start();
			?>
			<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#{id}">{buttonName}</button>
			<div id="{id}" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">{title}</h4>
						</div>
						<div class="modal-body">
							<p>{content}</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">{buttonClose}</button>
						</div>
					</div>
				</div>
			</div>	
			<?php
		return smart_replace(null, $options, true);
	}
	*/
	
	static function templateModal($options = null) {
		$optionsDefault = array(
			'id'			=>	'modal'.rand(0,999)*rand(0,999)	
		,	'buttonName'	=>	''
		,	'buttonClass'	=>	'btn btn-primary pull-right'
		,	'title'			=>	'buttonName'
		,	'content'		=>	'<p>use content para Conteudo de Texto</p>'
		,	'content.head'	=>	''
		,	'content.foot'	=>	''
		,	'content.file'	=>	''
		,	'buttonClose'	=>	'Fechar'
		,	'autoOpen'		=>	false
		,	'buttonConfirm'		=>	'OK'
		,	'buttonConfirm.onClick'		=>	'alert("OK!");'
		,	'buttonExtra'	=>	''
		);
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		ob_start();
		
		if(!empty($options['content.file'])) {
			if(file_exists($options['content.file'])){
				$options['content'] = file_get_contents($options['content.file']);
			};
		}
		
		if(!empty($options['buttonName'])) {
			?>
			<button type="button" class="{buttonClass}" data-toggle="modal" data-target="#{id}">{buttonName}</button>
			<?php
		}
		?>
		<div id="{id}" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					{content.head}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{title}</h4>
					</div>
					<div class="modal-body">
						{content}
					</div>
					<div class="modal-footer">
						<?php if(!empty($options['buttonExtra'])) { echo $options['buttonExtra']; } ?>
						<?php if(!empty($options['buttonConfirm'])) { ?>
						<button type="button" class="btn btn-primary">{buttonConfirm}</button>
						<?php } ?>
						<?php if(!empty($options['buttonClose'])) { ?>
						<button type="button" class="btn btn-default" data-dismiss="modal">{buttonClose}</button>
						<?php } ?>
					</div>
					{content.foot}
				</div>
			</div>
		</div>	
		<script>
		<?php if($options['autoOpen']) { ?>
				$('#{id}').modal('show');
		<?php } ?>
				
		<?php if(!empty($options['buttonConfirm.onClick'])) { ?>
		$('#{id}').find('button.btn-primary').on('click', function(){ {buttonConfirm.onClick} });
		<?php } ?>
				
		</script>
		<?php
		return smart_replace(null, $options, true);
	}
    
    static function alert($content, $classe = 'alert-danger') {
        return self::templateAlert(array(
            'content'  => $content
        ,   'classe'  => $classe
        ));
    }


    static function templateAlert($options = null) {
		$optionsDefault = array(
			'id'				=>	'alert'.rand(0,999)*rand(0,999)	
		,	'title'				=>	''
		,	'content'			=>	''
		,	'hasCloseButton'	=>	true
		,	'autoFechar'		=>	0
		,	'classe'		=>	'alert-danger'
		);
		$options = (is_array($options)) ? array_merge($optionsDefault, $options) : $optionsDefault;
		if(!empty($options['title'])) $options['title'] = '<h4>'.$options['title'].'</h4>';
		if(!empty($options['content'])) $options['content'] = '<p>'.$options['content'].'</p>';
		
		ob_start();
		?>
		<div id="{id}" class="alert {classe} alert-dismissible fade in" role="alert">
			<?php if($options['hasCloseButton']) echo '<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>'; ?>
			{title}{content}
			<!--//
			<p>
				<button type="button" class="btn btn-danger">{button1}</button>
				<button type="button" class="btn btn-default">{button2}</button>
			</p>
			//-->
		</div>		
		<?php
		if($options['autoFechar'] > 0) {
			?>
			<script>
			setTimeout( function(){
				$('#{id}').hide('slide');
			}, {autoFechar} );
			</script>
			<?php
		}
		
		return smart_replace(null, $options, true);
	}	
	
	static function toltip() {
		$args = func_get_args();
		$text = isset($args[0]) ? $args[0] : '';
		$placement = isset($args[1]) ? $args[1] : 'top';
		return 'data-toggle="tooltip" data-placement="'.$placement.'" title="'.$text.'"';
	}	
	
}
