<?php 
/** Fenom template '_footer.tpl' compiled at 2020-05-15 12:08:15 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><script>
    var langEndoSkill = <?php
/* _footer.tpl:2: {$lang.js|json_encode} */
 echo json_encode($var["lang"]["js"]); ?>
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="edit_skill" style="display: none; padding-top: 40px;">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content form-edit-skill">
            <div class="modal-header">
                <h4 class="modal-title"><?php
/* _footer.tpl:9: {$lang.editSkill} */
 echo $var["lang"]["editSkill"]; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label for="edit_title_skill" class=""><?php
/* _footer.tpl:13: {$lang.name} */
 echo $var["lang"]["name"]; ?></label>
                <input class="form-control" id="edit_title_skill" type="text" name="skill" value="">

                <select class="form-control" id="edit_group_skill" name="group_skill">
                    <option value="freelancer"><?php
/* _footer.tpl:17: {$lang.freelancer} */
 echo $var["lang"]["freelancer"]; ?></option>
                    <option value="employer"><?php
/* _footer.tpl:18: {$lang.employer} */
 echo $var["lang"]["employer"]; ?></option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
</body>
</html><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => '_footer.tpl',
	'base_name' => '_footer.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    '_footer.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
