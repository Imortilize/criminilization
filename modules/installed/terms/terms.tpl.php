<?php

/**
* A page to display your terms and conditions including some sample text
*
* @package terms
* @author NIF
* @version 1.0.0
*/

class termsTemplate extends template {

	public $terms = '
		{{terms}}
	';	

    public $options = '

        <form method="post" action="?page=admin&module=terms&action=terms">

            <div class="row">
                <div class="col-md-12">
                    <label class="">Terms & Conditions</label>
                    <div class="form-group">
                        <textarea type="text" class="form-control" name="termsOfUse" data-editor="html" rows="15">{termsOfUse}</textarea>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
            </div>
        </form>
    ';

}
