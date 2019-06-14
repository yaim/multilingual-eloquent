<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Models\Author;
use Tests\Models\Post;
use Tests\TestCase;

class ViewAuthorTest extends TestCase
{
    private function createAuthor()
    {
        $author = Author::create([
            'email' => 'sun.tzu@example.com',
            'name'  => 'Sun Tzu',
            'bio'   => 'Sun Tzu was a Chinese general, military strategist, writer and philosopher who lived in the Eastern Zhou period of ancient China.',
        ]);

        $author->setLocale('zh_Hans')->update([
            'name' => '孫子',
            'bio'  => '孫武（前545年－前470年）字長卿，春秋時期的著名軍事家、政治家，兵家代表人物。',
        ]);

        return $author;
    }

    private function createPost()
    {
        $author = $this->createAuthor();

        $post = $author->posts()->create([
            'title'   => 'The Art of War',
            'content' => 'The Art of War is an ancient Chinese military treatise dating from the Late Spring and Autumn Period.',
        ]);

        $post->setLocale('zh_Hans')->update([
            'title'   => '孫子兵法',
            'content' => '《孫子兵法》，即《孫子》，又稱作《武經》、《兵經》、《孫武兵法》、《吳孫子兵法》，是中國古代的兵書，作者為春秋末期的齊國人孫武（字長卿）。',
        ]);

        return $post;
    }

    private function assertAuthorData(int $authorID, string $email = 'sun.tzu@example.com')
    {
        $author = Author::find($authorID);

        $this->assertEquals($email, $author->email);
        $this->assertEquals('Sun Tzu', $author->name);
        $this->assertEquals('Sun Tzu was a Chinese general, military strategist, writer and philosopher who lived in the Eastern Zhou period of ancient China.', $author->bio);

        $chineseAuthor = Author::locale('zh_Hans')->first();

        $this->assertEquals($email, $chineseAuthor->email);
        $this->assertEquals('孫子', $chineseAuthor->name);
        $this->assertEquals('孫武（前545年－前470年）字長卿，春秋時期的著名軍事家、政治家，兵家代表人物。', $chineseAuthor->bio);

        $this->assertEquals(collect([
            'en_US',
            'zh_Hans',
        ]), $author->availableTranslatedLanguages());

        $this->assertEquals('author_translations', $author->getTranslationTable());
        $this->assertEquals('language_code', $author->getLanguageCodeName());
        $this->assertEquals('author_id', $author->getTranslationForeignKeyName());
    }

    /** @test */
    public function viewAuthorInDifferentLanguages()
    {
        $author = $this->createAuthor();

        $this->assertAuthorData($author->id);
    }

    /** @test */
    public function changingAuthorGenericDataWontChangeTranslations()
    {
        $author = $this->createAuthor();
        $author->update(['email' => 'sun_tzu@xample.com']);

        $this->assertAuthorData($author->id, 'sun_tzu@xample.com');
    }

    /** @test */
    public function viewPostInDifferentLanguages()
    {
        $generatedPost = $this->createPost();

        $post = Post::find($generatedPost->id);

        $this->assertEquals('The Art of War', $post->title);
        $this->assertEquals('The Art of War is an ancient Chinese military treatise dating from the Late Spring and Autumn Period.', $post->content);

        $chinesePost = Post::locale('zh_Hans')->find($generatedPost->id);

        $this->assertEquals('孫子兵法', $chinesePost->title);
        $this->assertEquals('《孫子兵法》，即《孫子》，又稱作《武經》、《兵經》、《孫武兵法》、《吳孫子兵法》，是中國古代的兵書，作者為春秋末期的齊國人孫武（字長卿）。', $chinesePost->content);

        $this->assertEquals(collect([
            'en_US',
            'zh_Hans',
        ]), $post->availableTranslatedLanguages());

        $this->assertEquals('multilingual_posts', $post->getTranslationTable());
        $this->assertEquals('translation_language_code', $post->getLanguageCodeName());
        $this->assertEquals('multilingual_post_id', $post->getTranslationForeignKeyName());
    }

    /** @test */
    public function deletePostWouldDeleteTranslations()
    {
        $post = $this->createPost();

        $this->assertEquals(2, DB::table('multilingual_posts')->count());
        $post->delete();

        $this->assertEquals(0, DB::table('multilingual_posts')->count());
    }
}
