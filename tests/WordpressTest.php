<?php

use PHPUnit\Framework\TestCase;
use Roliroli\WordpressTools\Models\Post;
use Roliroli\WordpressTools\TransformerFactory;

class WordpressTest extends TestCase
{
    /**
    * @test
    */
    public function it_can_access_posts()
    {
        $post = Post::find(1);

        if ($post) {
            $this->assertEquals($post->ID, 1);
        } else {
            $this->assertEquals($post, null);
        }
    }

    /**
    * @test
    */
    public function it_can_transform_posts()
    {
        $post = Post::with('author')->with('attachment')->type('post')->orderBy('post_date', 'desc')->published()->paged(1, 1)->first();
        $transformer = TransformerFactory::buildPostTransformer();
        $transformer->transformSingle($post);
        $this->assertTrue($post instanceof \Roliroli\WordpressTools\Models\Post);
    }

    /**
    * @test
    */
    public function it_can_transform_posts_with_amp()
    {
        $post = Post::with('author')->with('attachment')->type('post')->orderBy('post_date', 'desc')->published()->paged(1, 1)->first();
        $transformer = TransformerFactory::buildPostTransformer(['amp' => true]);
        $transformer->transformSingle($post);
        $this->assertTrue($post instanceof \Roliroli\WordpressTools\Models\Post);
    }
}