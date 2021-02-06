<?php


namespace Natsumework\RedisAutocomplete\Tests;

use Illuminate\Database\Eloquent\Collection;
use Natsumework\RedisAutocomplete\Autocomplete;
use ReflectionMethod;

class AutocompleteTest extends TestBase
{
    public function test_get_connection()
    {
        $reflector = new ReflectionMethod(Autocomplete::class, 'getConnection');
        $reflector->setAccessible(true);

        $this->assertSame($this->connection,
            $reflector->invoke(
                $this->autocomplete
            )
        );
    }

    public function test_set_connection()
    {
        $reflector = new ReflectionMethod(Autocomplete::class, 'getConnection');
        $reflector->setAccessible(true);

        $this->autocomplete->connection('auto-complete-connection');

        $this->assertSame('auto-complete-connection',
            $reflector->invoke(
                $this->autocomplete
            )
        );

        $this->autocomplete->connection(null);

        $this->assertSame(null,
            $reflector->invoke(
                $this->autocomplete
            )
        );

        $this->assertSame($this->connection,
            $reflector->invoke(
                $this->autocomplete
            )
        );
    }

    public function test_get_ttl()
    {
        $reflector = new ReflectionMethod(Autocomplete::class, 'getTtl');
        $reflector->setAccessible(true);

        $this->assertSame($this->ttl,
            $reflector->invoke(
                $this->autocomplete
            )
        );
    }

    public function test_set_ttl()
    {
        $reflector = new ReflectionMethod(Autocomplete::class, 'getTtl');
        $reflector->setAccessible(true);

        $this->autocomplete->ttl(5);

        $this->assertSame(5,
            $reflector->invoke(
                $this->autocomplete
            )
        );

        $this->autocomplete->ttl(null);

        $this->assertSame(null,
            $reflector->invoke(
                $this->autocomplete
            )
        );

        $this->assertSame($this->ttl,
            $reflector->invoke(
                $this->autocomplete
            )
        );
    }

    public function test_get_key()
    {
        $reflector = new ReflectionMethod(Autocomplete::class, 'getKey');
        $reflector->setAccessible(true);

        $name = 'my-autocomplete';
        $this->assertSame($this->prefix . ':' . $name,
            $reflector->invoke(
                $this->autocomplete,
                $name
            )
        );
    }

    public function test_add_and_search_phrases_by_array()
    {
        $phrase1 = [
            'id' => 1,
            'name' => 'phrase1',
        ];
        $phrase2 = [
            'id' => 2,
            'name' => 'phrase2',
            'other' => 'test',
            'score' => 1
        ];
        $test = [
            'id' => 3,
            'name' => 'test autocomplete'
        ];
        $chinese = [
            'id' => 4,
            'name' => '測試中文'
        ];

        $phrases = [
            $phrase1,
            $phrase2,
            $test,
            $chinese
        ];

        $this->autocomplete->addPhrases('test_add_and_search_phrases_by_array', $phrases);

        $this->assertSame([$phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_array', 'phrase1'));
        $this->assertSame([$phrase2, $phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_array', 'phrase'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_array', 'a'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_array', 'autocomplete'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_array', 't'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_array', 'test'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_array', '測'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_array', '測試'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_array', '測試中'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_array', '測試中文'));
    }

    public function test_add_and_search_phrases_by_collection()
    {
        $phrase1 = [
            'id' => 1,
            'name' => 'phrase1',
        ];
        $phrase2 = [
            'id' => 2,
            'name' => 'phrase2',
            'other' => 'test',
            'score' => 1
        ];
        $test = [
            'id' => 3,
            'name' => 'test autocomplete'
        ];
        $chinese = [
            'id' => 4,
            'name' => '測試中文'
        ];

        $phrases = collect([
            $phrase1,
            $phrase2,
            $test,
            $chinese
        ]);

        $this->autocomplete->addPhrases('test_add_and_search_phrases_by_collection', $phrases);

        $this->assertSame([$phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 'phrase1'));
        $this->assertSame([$phrase2, $phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 'phrase'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 'a'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 'autocomplete'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 't'));
        $this->assertSame([$test], $this->autocomplete->search('test_add_and_search_phrases_by_collection', 'test'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_collection', '測'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_collection', '測試'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_collection', '測試中'));
        $this->assertSame([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_collection', '測試中文'));
    }

    public function test_add_and_search_phrases_by_eloquent_collection()
    {
        $phrase1 = new Phrase();
        $phrase1->id = 1;
        $phrase1->name = 'phrase1';

        $phrase2 = new Phrase();
        $phrase2->id = 2;
        $phrase2->name = 'phrase2';
        $phrase2->other = 'test';
        $phrase2->score = 1;

        $test = new Phrase();
        $test->id = 3;
        $test->name = 'test autocomplete';

        $chinese = new Phrase();
        $chinese->id = 4;
        $chinese->name = '測試中文';

        $phrases = new Collection();
        $phrases->push($phrase1);
        $phrases->push($phrase2);
        $phrases->push($test);
        $phrases->push($chinese);

        $this->autocomplete->addPhrases('test_add_and_search_phrases_by_eloquent_collection', $phrases);

        $this->assertInstanceOf(Phrase::class, $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'phrase1')[0]);
        $this->assertEquals([$phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'phrase1'));
        $this->assertEquals([$phrase2, $phrase1], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'phrase'));
        $this->assertEquals([$test], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'a'));
        $this->assertEquals([$test], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'autocomplete'));
        $this->assertEquals([$test], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 't'));
        $this->assertEquals([$test], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', 'test'));
        $this->assertEquals([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', '測'));
        $this->assertEquals([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', '測試'));
        $this->assertEquals([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', '測試中'));
        $this->assertEquals([$chinese], $this->autocomplete->search('test_add_and_search_phrases_by_eloquent_collection', '測試中文'));
    }

    public function test_remove_phrase_by_array()
    {
        $phrase = [
            'id' => 1,
            'name' => 'phrase',
        ];

        $phrases = [$phrase];

        $this->autocomplete->addPhrases('test_remove_phrase_by_array', $phrases);
        $this->assertSame([$phrase], $this->autocomplete->search('test_remove_phrase_by_array', 'phrase'));

        $this->autocomplete->removePhrase('test_remove_phrase_by_array', $phrase);
        $this->assertSame([], $this->autocomplete->search('test_remove_phrase_by_array', 'phrase'));
    }

    public function test_remove_phrase_by_collection()
    {
        $phrase = [
            'id' => 1,
            'name' => 'phrase',
        ];

        $phrases = collect([$phrase]);

        $this->autocomplete->addPhrases('test_remove_phrase_by_collection', $phrases);
        $this->assertSame([$phrase], $this->autocomplete->search('test_remove_phrase_by_collection', 'phrase'));

        $this->autocomplete->removePhrase('test_remove_phrase_by_collection', $phrase);
        $this->assertSame([], $this->autocomplete->search('test_remove_phrase_by_collection', 'phrase'));
    }

    public function test_remove_phrase_by_eloquent_collection()
    {
        $phrase = new Phrase();
        $phrase->id = 1;
        $phrase->name = 'phrase';

        $phrases = new Collection();
        $phrases->push($phrase);

        $this->autocomplete->addPhrases('test_remove_phrase_by_eloquent_collection', $phrases);
        $this->assertEquals([$phrase], $this->autocomplete->search('test_remove_phrase_by_eloquent_collection', 'phrase'));

        $this->autocomplete->removePhrase('test_remove_phrase_by_eloquent_collection', $phrase);
        $this->assertSame([], $this->autocomplete->search('test_remove_phrase_by_eloquent_collection', 'phrase'));
    }

    public function test_phrase_will_expire_after_ttl()
    {
        $phrase = [
            'id' => 1,
            'name' => 'phrase',
        ];

        $phrases = [$phrase];

        $this->autocomplete
            ->ttl(5)
            ->addPhrases('test_remove_phrase_by_array', $phrases);
        $this->assertSame([$phrase], $this->autocomplete->search('test_remove_phrase_by_array', 'phrase'));

        sleep(5);

        $this->assertSame([], $this->autocomplete->search('test_remove_phrase_by_array', 'phrase'));
    }
}
