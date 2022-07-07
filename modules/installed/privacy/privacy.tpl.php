<?php

/**
* A page to display your privacy policy including some sample text
*
* @package privacy
* @author NIF
* @version 1.0.0
*/

class privacyTemplate extends template {

	public $privacy = '
		{{privacy}}
	';	

    public $options = '

        <form method="post" action="?page=admin&module=privacy&action=privacy">

            <div class="row">
                <div class="col-md-12">
                    <label class="">Privacy Policy</label>
                    <div class="form-group">
                        <textarea type="text" class="form-control" name="privacyPolicy" data-editor="html" rows="15">{privacyPolicy}</textarea>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
            </div>
        </form>
    ';

}
