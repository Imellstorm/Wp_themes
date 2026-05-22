<?php
/**
 * devpoint — one-shot content seeder.
 *
 * Creates 5 categories that match the design's palette + 11 Lorem ipsum posts.
 * Trigger: log in as admin, then visit /wp-admin/?devpoint_seed=run
 * Re-running is idempotent: existing posts/categories with the same slug
 * are skipped, so the seeder can be hit twice without producing duplicates.
 *
 * @package devpoint
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render an admin notice on the Posts list if the site has fewer than 6 posts.
 * Clicking it kicks off seeding.
 */
function devpoint_seed_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$count = (int) wp_count_posts()->publish;
	if ( $count >= 6 ) return;

	$url = wp_nonce_url(
		add_query_arg( 'devpoint_seed', 'run', admin_url( 'edit.php' ) ),
		'devpoint_seed'
	);
	?>
	<div class="notice notice-info">
		<p>
			<strong>devpoint:</strong>
			<?php esc_html_e( "It looks like you don't have many posts yet — want to seed 11 Lorem ipsum essays across 5 sample categories?", 'devpoint' ); ?>
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary" style="margin-left:8px;">
				<?php esc_html_e( 'Seed sample content', 'devpoint' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'devpoint_seed_admin_notice' );

/**
 * Handle the seed request.
 */
function devpoint_seed_handle() {
	if ( ! isset( $_GET['devpoint_seed'] ) || $_GET['devpoint_seed'] !== 'run' ) return;
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden.' );
	check_admin_referer( 'devpoint_seed' );

	$result = devpoint_seed_run();

	set_transient( 'devpoint_seed_result', $result, 60 );
	wp_safe_redirect( admin_url( 'edit.php?devpoint_seeded=1' ) );
	exit;
}
add_action( 'admin_init', 'devpoint_seed_handle' );

/**
 * Show post-seed result notice.
 */
function devpoint_seed_result_notice() {
	if ( ! isset( $_GET['devpoint_seeded'] ) ) return;
	$res = get_transient( 'devpoint_seed_result' );
	delete_transient( 'devpoint_seed_result' );
	if ( ! $res ) return;
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<strong>devpoint:</strong>
			<?php
			/* translators: 1: post count, 2: category count, 3: sticky count */
			printf(
				esc_html__( 'Seeded %1$d posts across %2$d categories. %3$d marked as sticky for the Editor\'s picks block.', 'devpoint' ),
				(int) $res['posts'],
				(int) $res['cats'],
				(int) $res['sticky']
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'devpoint_seed_result_notice' );

/**
 * Actually create the categories and posts.
 *
 * @return array { posts, cats, sticky }
 */
function devpoint_seed_run() {
	$category_specs = [
		[ 'slug' => 'web',    'name' => 'Web Development',   'description' => 'Building sites that earn their keep — design, performance, and the unglamorous middle bits.' ],
		[ 'slug' => 'mobile', 'name' => 'Mobile Apps',        'description' => 'iOS, Android, and the road from idea to App Store.' ],
		[ 'slug' => 'biz',    'name' => 'IT for Business',    'description' => 'How software decisions actually move business metrics.' ],
		[ 'slug' => 'pm',     'name' => 'Project Management', 'description' => 'Shipping software with predictable timelines, without burning the team.' ],
		[ 'slug' => 'cases',  'name' => 'Case Studies',       'description' => 'Real engagements with real numbers — what worked, what didn\'t.' ],
	];

	$cats_created = 0;
	$cat_ids      = [];
	foreach ( $category_specs as $spec ) {
		$existing = get_term_by( 'slug', $spec['slug'], 'category' );
		if ( $existing ) {
			$cat_ids[ $spec['slug'] ] = (int) $existing->term_id;
			continue;
		}
		$res = wp_insert_term( $spec['name'], 'category', [
			'slug'        => $spec['slug'],
			'description' => $spec['description'],
		] );
		if ( ! is_wp_error( $res ) ) {
			$cat_ids[ $spec['slug'] ] = (int) $res['term_id'];
			$cats_created++;
		}
	}

	$post_specs = [
		[ 'cat' => 'web',    'title' => 'How much does a website cost in 2026 — a no-jargon guide',          'sticky' => true ],
		[ 'cat' => 'mobile', 'title' => 'From idea to App Store — the launch playbook we use with founders', 'sticky' => true ],
		[ 'cat' => 'biz',    'title' => 'What an MVP actually is — and the 3 traps founders fall into',      'sticky' => true ],
		[ 'cat' => 'biz',    'title' => 'How to choose your development partner — 7 red flags & 4 green ones', 'sticky' => true ],
		[ 'cat' => 'web',    'title' => 'Designing for trust — the small details that close sales' ],
		[ 'cat' => 'web',    'title' => 'Headless CMS, explained without the jargon' ],
		[ 'cat' => 'biz',    'title' => "Why your landing page isn't converting — diagnosed in 8 steps" ],
		[ 'cat' => 'biz',    'title' => 'AI in customer support — where it actually works (and where it doesn\'t)' ],
		[ 'cat' => 'pm',     'title' => 'The hidden cost of cheap development — a $40k retrospective' ],
		[ 'cat' => 'pm',     'title' => 'Rebuild or refactor? A decision tree for product owners' ],
		[ 'cat' => 'mobile', 'title' => "Apple's 2026 App Store changes — what founders need to know" ],
	];

	$lorem = devpoint_seed_lorem();

	$posts_created = 0;
	$sticky_ids    = [];
	$base_time     = current_time( 'timestamp' ) - DAY_IN_SECONDS;
	$i             = 0;

	foreach ( $post_specs as $spec ) {
		$slug = sanitize_title( $spec['title'] );
		if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
			$i++; continue;
		}
		$post_id = wp_insert_post( [
			'post_title'   => $spec['title'],
			'post_content' => $lorem[ $i % count( $lorem ) ],
			'post_excerpt' => devpoint_seed_excerpt( $i ),
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_name'    => $slug,
			'post_date'    => gmdate( 'Y-m-d H:i:s', $base_time - $i * DAY_IN_SECONDS * 2 ),
			'post_author'  => get_current_user_id(),
		], true );
		if ( ! is_wp_error( $post_id ) && $post_id ) {
			if ( isset( $cat_ids[ $spec['cat'] ] ) ) {
				wp_set_post_categories( $post_id, [ $cat_ids[ $spec['cat'] ] ] );
			}
			if ( ! empty( $spec['sticky'] ) ) {
				$sticky_ids[] = $post_id;
			}
			$posts_created++;
		}
		$i++;
	}

	if ( ! empty( $sticky_ids ) ) {
		$current = get_option( 'sticky_posts', [] );
		update_option( 'sticky_posts', array_values( array_unique( array_merge( $current, $sticky_ids ) ) ) );
	}

	return [
		'posts'  => $posts_created,
		'cats'   => $cats_created,
		'sticky' => count( $sticky_ids ),
	];
}

/**
 * 11 Lorem ipsum bodies. Mix of paragraphs, an h2, a blockquote, and a list,
 * so posts exercise every styled element in the reading-room layout.
 */
function devpoint_seed_lorem() {
	$P = [
		'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a est nec lectus efficitur dictum vitae sed est. Mauris feugiat, lacus a iaculis tincidunt, ligula dolor pulvinar urna, ut consectetur magna libero a mi.',
		'Praesent ac purus a turpis aliquet faucibus. Integer at quam at urna scelerisque hendrerit non vitae lacus. Nullam vitae odio sit amet enim aliquet ultrices a non quam. Sed sit amet diam id ipsum hendrerit blandit.',
		'Sed in justo at lectus tristique laoreet a id leo. Etiam vehicula libero in sapien ultricies, et facilisis quam pretium. Aliquam erat volutpat. Mauris vel arcu posuere, fringilla magna a, varius felis.',
		'Donec euismod, dolor non suscipit pulvinar, nisl ipsum tincidunt risus, sit amet luctus erat libero in lectus. In hac habitasse platea dictumst. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.',
		'Vivamus ornare nibh nec mi pulvinar, ut sagittis libero faucibus. Aenean quis turpis libero. Etiam non eros at nulla suscipit fermentum non eget purus. Phasellus convallis purus vel diam dignissim, vitae luctus magna placerat.',
	];
	$bodies = [];
	for ( $n = 0; $n < 11; $n++ ) {
		$body  = '<p>' . $P[ $n % 5 ] . '</p>';
		$body .= '<p>' . $P[ ( $n + 1 ) % 5 ] . '</p>';
		$body .= '<h2>' . [ 'Start with the outcome', 'Where the budget goes', 'What to ask before signing', 'A note on timelines', 'Beyond the launch' ][ $n % 5 ] . '</h2>';
		$body .= '<p>' . $P[ ( $n + 2 ) % 5 ] . '</p>';
		$body .= '<blockquote>' . $P[ ( $n + 3 ) % 5 ] . '</blockquote>';
		$body .= '<p>' . $P[ ( $n + 4 ) % 5 ] . '</p>';
		$body .= '<ul><li>' . substr( $P[0], 0, 60 ) . '…</li><li>' . substr( $P[1], 0, 60 ) . '…</li><li>' . substr( $P[2], 0, 60 ) . '…</li></ul>';
		$body .= '<p>' . $P[ $n % 5 ] . '</p>';
		$bodies[] = $body;
	}
	return $bodies;
}

function devpoint_seed_excerpt( $i ) {
	$excerpts = [
		'A practical breakdown of what you\'re really paying for: scope, complexity, integrations, and the hidden costs that surprise most founders.',
		'The four phases that decide whether your app gets stuck in development hell or shipped to real users. With timelines.',
		'An MVP isn\'t "a smaller version of your product." It\'s a learning instrument. Here\'s how to scope one that earns its name.',
		'Cheap quotes, vague timelines, no portfolio depth: a quick framework for vetting agencies before you sign anything.',
		'Microcopy, social proof placement, and the friction patterns that quietly convert browsers into buyers.',
		'When it actually helps your business, when it just adds cost, and how to decide for your team.',
		'A teardown checklist we run on every client homepage. Most fail on point #3.',
		'An honest field report from 12 deployments. The wins are smaller and stranger than the demos suggest.',
		'We rebuilt a $12k app for $40k. Here\'s where every dollar of the difference went and why.',
		'Three questions to ask before greenlighting a v2. The answers usually surprise the team.',
		'Pricing, review timelines, and the new "Essentials" tier. We translated the developer release notes into plain English.',
	];
	return $excerpts[ $i % count( $excerpts ) ];
}
