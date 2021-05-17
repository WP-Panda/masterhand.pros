<script>
    var langEndoSkill = {$lang.js|json_encode}
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="edit_skill" style="display: none; padding-top: 40px;">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content form-edit-skill">
            <div class="modal-header">
                <h4 class="modal-title">{$lang.editSkill}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label for="edit_title_skill" class="">{$lang.name}</label>
                <input class="form-control" id="edit_title_skill" type="text" name="skill" value="">

                <select class="form-control" id="edit_group_skill" name="group_skill">
                    <option value="freelancer">{$lang.freelancer}</option>
                    <option value="employer">{$lang.employer}</option>
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
</html>