<?php 
/** Fenom template 'main.tpl' compiled at 2020-08-14 05:33:17 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php
/* main.tpl:1: {include '_head.tpl'} */
 $tpl->getStorage()->getTemplate('_head.tpl')->display($var); ?>
	<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="<?php
/* main.tpl:2: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>">
		<i class="fas fa-sync-alt"><?php
/* main.tpl:3: {$lang.refresh} */
 echo $var["lang"]["refresh"]; ?></i></a>
	
		

	
		
			
		
		
			
		
	
	<div class="tab-content" id="tabs">
		<div class="tab-pane active show" id="post_like">
			<div class="row">
				<div class="col-md-5">
				<div class="input-group mb-3">
					<input type="text" class="form-control" id="search_field" placeholder="<?php
/* main.tpl:20: {$lang.search} */
 echo $var["lang"]["search"]; ?>...">
					<div class="input-group-append">
						<button class="btn btn-info run-search">
							<i class="fas fa-search"></i>
						</button>
						<button class="btn btn-secondary reset-search">
							<?php
/* main.tpl:26: {$lang.reset} */
 echo $var["lang"]["reset"]; ?>
						</button>
					</div>
				</div>
				<table class="table table-hover source-table-items">
					<thead class="font-weight-bold">
					<tr>
						<td class="col-sort" onclick="mod.setSort(this, 'ID')"><i class="fas"><?php
/* main.tpl:33: {$lang.userId} */
 echo $var["lang"]["userId"]; ?></i></td>
						<td class="col-sort" onclick="mod.setSort(this, 'post_title')"><i class="fas"> <?php
/* main.tpl:34: {$lang.docTitle} */
 echo $var["lang"]["docTitle"]; ?></i></td>
						<td class="col-sort" onclick="mod.setSort(this, 'likes')"><i class="fas"> <?php
/* main.tpl:35: {$lang.countLikes} */
 echo $var["lang"]["countLikes"]; ?></i></td>
						<td class=""></td>
						<td class=""></td>
					</tr>
					</thead>
					<tbody id="listItems">
					<?php
/* main.tpl:41: {include 'list_post.tpl'} */
 $tpl->getStorage()->getTemplate('list_post.tpl')->display($var); ?>
					</tbody>
					<tfoot>
					<tr>
						<td class="source-pagination-items" colspan="5">
							<?php
/* main.tpl:46: {$postPagination} */
 echo $var["postPagination"]; ?>
						</td>
					</tr>
					</tfoot>
				</table>
				</div>
				<div class="col-md-7">
					<div class="row">
					<div class="col-xs-6 wrap-chartPie" style="width: 300px; height: 300px">
						<canvas id="chartPie" style="width: 100%;height: 100%"></canvas>
					</div>
					<div class="col-xs-6 wrap-chartLine" style="width: 400px; height: 300px">
						<canvas id="chartLine" style="width: 100%;height: 100%"></canvas>
					</div>
					</div>
				</div>
			</div>
		</div>

		
			
				
				
					
						
					
					
						
					
				
			
			
				
				
					
					
					
					
					
					
					
					
					
				
				
				
				
				
				
				
					
						
					
				
				
			
		
	</div>
<?php
/* main.tpl:104: {include '_footer.tpl'} */
 $tpl->getStorage()->getTemplate('_footer.tpl')->display($var); ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'main.tpl',
	'base_name' => 'main.tpl',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'main.tpl' => 1588253052,
  ),
),
	'macros' => array(),

        ));
