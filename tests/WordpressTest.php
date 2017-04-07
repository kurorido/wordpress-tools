<?php

use PHPUnit\Framework\TestCase;
use Corcel\Database;
use Roliroli\WordpressTools\Models\Post;
use Roliroli\WordpressTools\TransformerFactory;

class WordpressTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        Database::connect($params = [
            'database' => getenv('DATABASE') ?? 'corel',
            'username' => getenv('DATABASE_USER') ?? 'corel',
            'password' => getenv('DATABASE_PASSWORD') ?? 'corel',
            'host' => getenv('DATABASE_HOST') ?? '127.0.0.1'
        ]);
    }

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
        $this->assertEquals($post instanceof \Roliroli\WordpressTools\Models\Post);
    }

    /**
    * @test
    */
    public function it_can_transform_posts_with_amp()
    {
        $post = Post::with('author')->with('attachment')->type('post')->orderBy('post_date', 'desc')->published()->paged(1, 1)->first();
        $transformer = TransformerFactory::buildPostTransformer(['amp' => true]);
        $transformer->transformSingle($post);
        $this->assertEquals($post instanceof \Roliroli\WordpressTools\Models\Post);
    }
}