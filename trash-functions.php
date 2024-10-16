<?php 
// // delete trash posts
// $posts = new WP_Query(array(
//     'post_type' => 'post',
//     'post_status' => array('trash'),
//     'posts_per_page' => -1  // Get all posts
// ));
// // Get the number of posts
// $total_posts = $posts->found_posts;
// $i = 0;
// while ($i < $total_posts) {
//     if ($posts->have_posts()) {
//         while ($posts->have_posts()) {
//             $posts->the_post();
//             $post_id = get_the_ID();
//            try{
//             wp_delete_post($post_id);
//             echo "POST DELETED";
//            }catch(Exception $e){
//             echo $e->getMessage();
//            }
//         }
//     }
//     $i++;
// }
?>