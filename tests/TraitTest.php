<?php

use Illuminate\Support\Str;
use Kiwilan\Steward\Services\SlugService;
use Kiwilan\Steward\Tests\Data\Models\Book;

it('can use slug', function () {
    $book = book();

    expect($book->title)->toBe(bookTitle());
    expect($book->author)->toBe('J. R. R. Tolkien');
    expect($book->description_custom)->toBe(bookDescription());
    expect($book->slug_custom)->toBe(SlugService::make(bookTitle()));

    $book2 = book();

    expect($book2->title)->toBe(bookTitle());
    expect($book2->slug_custom)->toBe(SlugService::make(bookTitle()).'-1');
});

it('can use seo', function () {
    $book = book();

    expect($book->meta_title)->toBe(bookTitle());
    expect($book->meta_description)->toBe(bookDescription());

    expect($book->seo)->toBeArray();
    expect($book->seo['title'])->toBe(bookTitle());
    expect($book->seo['description'])->toBe(bookDescription());

    $description = 'Aliquip duis deserunt ea eiusmod excepteur pariatur velit qui voluptate. Ipsum consectetur amet qui officia minim. Cillum aliqua et tempor ea anim culpa. Duis Lorem est commodo sit velit veniam ipsum nisi enim proident cillum Lorem. Incididunt veniam elit eu proident consequat culpa eu velit dolor. Qui cupidatat occaecat do exercitation esse elit laboris. Culpa consectetur nulla anim cillum ut sit veniam dolor duis ea ullamco amet. Labore eiusmod laborum sit ad dolor ut eiusmod in culpa sunt exercitation. Laboris incididunt occaecat nisi eiusmod cupidatat elit aute. Do sit culpa elit aliqua enim dolor. Nulla aliquip incididunt qui eu reprehenderit nulla est. Veniam deserunt sint laboris velit exercitation adipisicing officia eiusmod velit dolor pariatur officia aliquip. Aute ullamco occaecat enim qui aliquip.';
    $metaDescription = Str::limit($description, 250, '...');
    $book2 = Book::create([
        'title' => 'The Lord of the Rings',
        'description_custom' => $description,
    ]);

    expect($book2->meta_description)->toHaveLength(253);
    expect($book2->meta_description)->toBe($metaDescription);

    $newDescription = 'Nisi ea nostrud ipsum nostrud in mollit. Ad et id excepteur Lorem consequat laborum aliquip deserunt in minim do sunt excepteur Lorem. Nostrud proident officia excepteur aliqua irure sit. Occaecat labore eiusmod duis commodo velit ea amet do. Consectetur reprehenderit exercitation nostrud mollit enim veniam duis.';
    $book2->description_custom = $newDescription;
    $book2->save();
    expect($book2->meta_description)->toBe($metaDescription);

    $book2->meta_description = $newDescription;
    $book2->save();

    $newMetaDescription = Str::limit($newDescription, 250, '...');
    expect($book2->meta_description)->toBe($newMetaDescription);
});

it('can use time to read', function () {
    $book = book();

    $body_custom = 'Fugiat occaecat minim cupidatat fugiat qui aute velit sint. Duis dolor eiusmod incididunt aliquip aute ea sint quis mollit. Ad eiusmod eu do laboris irure ipsum cupidatat duis. Sunt consectetur qui excepteur ea laborum tempor consectetur dolor.\nNostrud minim pariatur velit dolor ex qui do officia. Labore in consequat ullamco commodo veniam. Elit quis laborum pariatur Lorem cillum commodo qui sunt veniam amet amet ea exercitation.\nNon eu commodo eiusmod incididunt tempor non ea sit. Esse do consectetur qui eu amet cillum dolor magna qui ipsum. Laboris et esse cillum cupidatat laborum quis cupidatat culpa nulla eu excepteur consequat. Fugiat proident sit qui et quis amet Lorem elit do laborum.\nSint in magna duis aliquip labore reprehenderit anim cillum irure velit cupidatat aute veniam. Pariatur elit consectetur laboris dolor consequat reprehenderit ipsum aliquip. Eu ut esse elit culpa laboris deserunt culpa officia non aute quis. Cupidatat in in anim mollit ipsum nostrud. Ea eiusmod quis in eiusmod qui anim adipisicing do nostrud.\nConsequat adipisicing mollit consequat aute anim incididunt quis sunt elit consectetur mollit aute. In aliqua anim magna cupidatat excepteur excepteur ipsum. Dolore ex quis qui culpa. Esse reprehenderit sunt sunt laboris culpa velit amet nostrud laborum velit ipsum ullamco magna Lorem. Eiusmod dolore eiusmod duis irure mollit excepteur eu mollit eu fugiat irure sint pariatur.\nSit in tempor magna aute. Do id quis nostrud sit in consequat esse reprehenderit. Anim adipisicing consectetur consectetur ea. Aliquip sint qui tempor commodo nulla est do consectetur aute.\nVoluptate qui laboris laboris sunt sit fugiat. Incididunt dolore irure culpa ex nisi excepteur qui. Laborum sit sint labore veniam. Exercitation ipsum non aute quis. Et cupidatat ex sint quis. Quis est ea exercitation exercitation officia et nostrud officia. Aliquip mollit enim tempor sint duis sit exercitation irure.\nIn eiusmod magna enim veniam magna deserunt. Sint tempor fugiat culpa adipisicing sint ullamco deserunt id ad consectetur quis dolore anim. Anim ipsum deserunt sit sit. Pariatur commodo enim esse esse deserunt mollit nostrud anim. Mollit duis veniam exercitation ullamco non nostrud sit veniam ullamco quis do. Ullamco eiusmod voluptate est magna voluptate nulla quis Lorem nostrud. In sit culpa occaecat occaecat ex.\nMagna commodo pariatur qui cupidatat. Laborum magna culpa consequat nulla reprehenderit eiusmod veniam fugiat sunt veniam qui duis excepteur commodo. Non adipisicing consectetur non ut est. Exercitation excepteur esse Lorem consequat qui proident consectetur dolore qui esse nisi in. Ad ex amet dolor do enim quis occaecat mollit proident minim quis nisi. Nostrud reprehenderit incididunt incididunt ex laboris elit elit. Sit culpa velit consequat qui commodo magna labore laborum occaecat duis aute.';
    $book->body_custom = $body_custom;
    $book->save();

    expect($book->body_custom)->toBe($body_custom);
    expect($book->time_to_read_custom)->toBe(60);
    expect($book->time_to_read_minutes)->toBe(1);
});
