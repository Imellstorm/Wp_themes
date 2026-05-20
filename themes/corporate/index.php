<?php get_header(); ?>

<main class="section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div><?php the_excerpt(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No content found.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
