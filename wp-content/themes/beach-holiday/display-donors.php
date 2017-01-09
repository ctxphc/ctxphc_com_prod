<?php
/**
 * Template Name: Display Donors
 *
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 7/16/2016
 * Time: 8:16 AM
 */

get_header();


$type = 'donors';
$args = array(
	'post_type'        => $type,
	'post_status'      => 'publish',
	'posts_per_page'   => - 1,
	'caller_get_posts' => 1,
	'orderby'          => 'title',
	'order'            => 'ASC',
);

$my_query = null;
$my_query = new WP_Query( $args ); ?>
	<div id="content">
		<div class="spacer"></div>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post_title">
					<h1><a href="<?php the_permalink() ?>" rel="bookmark"
					       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				</div> <!-- post_title -->
				<div class="clear"></div>
				<div class="entry">
					<div class="spacer"></div>
					<h1 class="pb_donors">Thank You</h1>
					<div class="pb_donors">
						<h3 class="pb_donors">
							Pirate's Ball is a success, not only because of all the helping hands in putting it
							together, and
							the
							PHun-loving and caring attendees, but because of these generous
						</h3>
						<h3 class="pb_donors">Silent Auction and Raffle Donors:</h3>
					</div>
					<?php
					$post_count  = $my_query->post_count;
					$count_posts = 0;
					
					echo '<table class="pb_donors"><tbody>';
					
					if ( $my_query->have_posts() ) {
						
						while ( $my_query->have_posts() ) {
							
							$count_posts ++;
							if ( ( $count_posts % 2 ) == 1 ) {
								
								if ( $count_posts <= 1 ) {
									echo '<tr id="first_row" class="pb_donors">';
								} else if ( $post_count - $count_posts <= 1 ) {
									echo '<tr id="last_row" class="pb_donors">';
								} else {
									echo '<tr class="pb_donors">';
								}
								
								echo '<td class="pb_donors">';
								$my_query->the_post();
								the_title();
								echo '</td>';
								
							} else {
								
								echo '<td class="pb_donors">';
								$my_query->the_post();
								the_title();
								echo '</td>';
								echo '</tr>';
							}
						}
					}
					if ( ( $count_posts % 2 ) == 1 ) {
						echo '<td class="pb_donors"></td>';
						echo '</tr>';
					}
					echo '</table></tbody>';
					wp_reset_query();  // Restore global post data stomped by the_post().?>
					<div class="clear"></div>
				</div> <!-- entry -->
			</div> <!-- post -->
			<?php
		endwhile;
		endif;
		?>
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>