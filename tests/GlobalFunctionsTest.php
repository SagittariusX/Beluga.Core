<?php


/**
 * @author         UniKado <unikado+pubcode@protonmail.com>
 * @copyright  (c) 2016, UniKado
 * @since          2016-08-04
 * @version        0.1.0
 */
class GlobalFunctionsTest extends PHPUnit_Framework_TestCase
{

   public function setUp()
   {
      # TODO initial code here
   }

   public function testStrLen()
   {
      $this->assertSame( 0,  \Beluga\strLen( '' )                          , 'Assert 1 fails' );
      $this->assertSame( 2,  \Beluga\strLen( '\0' )                        , 'Assert 2 fails' );
      $this->assertSame( 1,  \Beluga\strLen( "\0" )                        , 'Assert 3 fails' );
      $this->assertSame( 22, \Beluga\strLen( "äöüÄÖÜß–—…÷·×@ł€¶ŧ←↓→ø" )    , 'Assert 4 fails' );
      $this->assertSame( 1,  \Beluga\strLen( 'ä' )                         , 'Assert 5 fails' );
      $this->assertSame( 7,  \Beluga\strLen( 'äAöÖüÜß' )                   , 'Assert 6 fails' );
      $this->assertSame( 26, \Beluga\strLen( '»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ' ), 'Assert 7 fails' );
   }

   public function testSubstr()
   {
      $this->assertEquals( 'ä'  , \Beluga\substr( 'ä…', 0, 1 )                        , 'Assert 1 fails' );
      $this->assertEquals( 'ä'  , \Beluga\substr( 'ä…', 0, -1 )                       , 'Assert 2 fails' );
      $this->assertEquals( '…'  , \Beluga\substr( 'ä…', 1 )                           , 'Assert 3 fails' );
      $this->assertEquals( '…'  , \Beluga\substr( 'ä…', 1, 2 )                        , 'Assert 4 fails' );
      $this->assertEquals( 'äAö', \Beluga\substr( 'äAöÖüÜß', 0, -4 )                  , 'Assert 5 fails' );
      $this->assertEquals( 'ö'  , \Beluga\substr( 'äAöÖüÜß', 2, -4 )                  , 'Assert 6 fails' );
      $this->assertEquals( '·…' , \Beluga\substr( '»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ', 7, 2 ), 'Assert 7 fails' );
   }

   public function testStrPos()
   {
      $this->assertSame ( 3, \Beluga\strPos( '-äx…ü', '…' )                       , 'Assert 1 fails' );
      $this->assertSame ( 1, \Beluga\strPos( 'äAöÖüÜß', 'AÖ', true )              , 'Assert 2 fails' );
      $this->assertSame ( 9, \Beluga\strPos( '»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ', '–ĸŋ' ), 'Assert 3 fails' );
      $this->assertFalse(    \Beluga\strPos( 'äAöÖüÜß', 'AÖ' )                    , 'Assert 4 fails' );
   }

   public function testStrLastPos()
   {
      $this->assertSame ( 5 , \Beluga\strLastPos( '-äx…ü…', '…' )                         , 'Assert 1 fails' );
      $this->assertSame ( 5 , \Beluga\strLastPos( 'äAöÖüaÖÜß', 'AÖ', true )               , 'Assert 2 fails' );
      $this->assertSame ( 18, \Beluga\strLastPos( '»«¢„“”µ·…–ĸŋđðſæ@ł–ĸŋ€¶ŧ←↓→øþ', '–ĸŋ' ), 'Assert 3 fails' );
      $this->assertFalse(     \Beluga\strLastPos( 'äAöÖüÜß', 'AÖ' )                       , 'Assert 4 fails' );
   }

   public function testStrPositions()
   {
      $this->assertEquals( [ 3, 5 ],  \Beluga\strPositions( '-äx…ü…', '…' ), 'Assert 1 fails' );
      $this->assertFalse (            \Beluga\strPositions( 'äAöÖüÜß', 'AÖ' ), 'Assert 2 fails' );
      $this->assertEquals( [ 1, 5 ],  \Beluga\strPositions( 'äAöÖüaÖÜß', 'AÖ', true ), 'Assert 3 fails' );
      $this->assertEquals( [ 9, 18 ], \Beluga\strPositions( '»«¢„“”µ·…–ĸŋđðſæ@ł–ĸŋ€¶ŧ←↓→øþ', '–ĸŋ' ), 'Assert 4 fails' );
   }

   public function testStrStartsWith()
   {
      $this->assertTrue ( \Beluga\strStartsWith( '·-äx…ü…', '·-' )                     , 'Assert 1 fails' );
      $this->assertFalse( \Beluga\strStartsWith( 'AöÖüÜß', 'AÖ' )                      , 'Assert 2 fails' );
      $this->assertTrue ( \Beluga\strStartsWith( 'AöÖüaÖÜß', 'AÖ', true )              , 'Assert 3 fails' );
      $this->assertTrue ( \Beluga\strStartsWith( '»«¢„“”µ·…–ĸŋđðſæ@ł–ĸŋ€¶ŧ←↓→øþ', '»' ), 'Assert 4 fails' );
      $this->assertTrue ( \Beluga\strStartsWith( 'ł–ĸŋ€¶ŧ←↓→øþ', 'ł' )                 , 'Assert 5 fails' );
   }

   public function testStrEndsWith()
   {
      $this->assertTrue ( \Beluga\strEndsWith( '·-äx…ü…', 'ü…' )                     , 'Assert 1 fails' );
      $this->assertFalse( \Beluga\strEndsWith( 'AöÖüÜß', 'üß' )                      , 'Assert 2 fails' );
      $this->assertTrue ( \Beluga\strEndsWith( 'AöÖüaÖÜß', 'üß', true )              , 'Assert 3 fails' );
      $this->assertTrue ( \Beluga\strEndsWith( '»«¢„“”µ·…–ĸŋđðſæ@ł–ĸŋ€¶ŧ←↓→øþ', 'þ' ), 'Assert 4 fails' );
      $this->assertTrue ( \Beluga\strEndsWith( 'ł–ĸŋ€¶ŧ←↓→øþ', '→øþ' )               , 'Assert 5 fails' );
   }

   public function testStrContains()
   {
      $this->assertTrue ( \Beluga\strContains( '·-äx…ü…', 'äx…' )                    , 'Assert 1 fails' );
      $this->assertFalse( \Beluga\strContains( 'AöÖüÜß', 'ööÜ' )                     , 'Assert 2 fails' );
      $this->assertTrue ( \Beluga\strContains( 'AöÖüaÖÜß', 'ööÜ', true )             , 'Assert 3 fails' );
      $this->assertTrue ( \Beluga\strContains( '»«¢„“”µ·…–ĸŋđðſæ@ł–ĸŋ€¶ŧ←↓→øþ', 'æ' ), 'Assert 4 fails' );
      $this->assertTrue ( \Beluga\strContains( 'ł–ĸŋ€¶ŧ←↓→øþ', '←↓→' )               , 'Assert 5 fails' );
   }

   public function testEscapeXML()
   {
      $this->assertEquals(
         '&lt;foo id="12"&gt; &amp;äöü',
         \Beluga\escapeXML( '<foo id="12"> &äöü' ),
         'Assert 1 fails' );
   }

   public function testEscapeXMLArg()
   {
      $this->assertEquals(
         '&lt;foo id=&quot;12&quot; b=&#39;0&#39;&gt; &amp;äöü',
         \Beluga\escapeXMLArg( '<foo id="12" b=\'0\'> &äöü' ),
         'Assert 1 fails' );
   }

   public function testEscape()
   {
      $this->assertEquals(
         '&lt;foo id=&quot;12&quot; b=&#39;0&#39;&gt; &amp;äöü',
         \Beluga\escape( '<foo id="12" b=\'0\'> &äöü', \Beluga\ESCAPE_HTML_ALL ),
         'Assert 1 fails' );
      $this->assertEquals(
         '%3Cfoo+id%3D%2212%22+b%3D%270%27%3E+%3Fx%3D%E2%80%A6%26%C3%A4%C3%B6%C3%BC%26a14%3D1',
         \Beluga\escape( '<foo id="12" b=\'0\'> ?x=…&äöü&a14=1', \Beluga\ESCAPE_URL ),
         'Assert 2 fails' );
      $this->assertEquals(
         '"Foo bar baz \"\" Bl\u00fcb \u2026\u00b7\u201d\u201c\u201e\u00a2\u00ab\u00bb|\u00e6\u017f\u00f0\u0111\u014b\u0127"',
         \Beluga\escape( 'Foo bar baz "" Blüb …·”“„¢«»|æſðđŋħ', \Beluga\ESCAPE_JSON ),
         'Assert 3 fails' );
      $this->assertEquals(
         '&lt;foo id="12" b=\'0\'&gt; &amp;äöü',
         \Beluga\escape( '<foo id="12" b=\'0\'> &äöü', \Beluga\ESCAPE_HTML ),
         'Assert 4 fails' );
   }

   public function testUnescapeXML()
   {
      $this->assertEquals(
         '<foo id="12" b=\'0\'> &äöü',
         \Beluga\unescapeXML( '&lt;foo id=&quot;12&quot; b=&#39;0&#39;&gt; &amp;äöü' ),
         'Assert 1 fails' );
   }

   public function testStrMax()
   {
      $this->assertEquals(
         'aÖ»«¢„…',
         \Beluga\strMax( 'aÖ»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ', 7, '…' ),
         'Assert 1 fails' );
      $this->assertEquals(
         'aÖ»«¢„',
         \Beluga\strMax( 'aÖ»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ', 6, '' ),
         'Assert 2 fails' );
   }

   public function testStrIReplace()
   {
      $this->assertEquals(
         'a--øþ',
         \Beluga\strIReplace( 'Ö»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→', '--', 'aÖ»«¢„“”µ·…–ĸŋđðſæ@ł€¶ŧ←↓→øþ' ),
         'Assert 1 fails' );
      $this->assertEquals(
         'äÄÜß',
         \Beluga\strIReplace( 'öÖÜ', '', 'äÄöÖüÜß' ),
         'Assert 2 fails' );
      $this->assertEquals(
         'äÄøÆöÖüÜß',
         \Beluga\strIReplace( 'øÆ', '', 'äÄøØæÆöøæÖüÜß' ),
         'Assert 3 fails' );
      $this->assertEquals(
         'äÄøØæÆöÖüÜß',
         \Beluga\strIReplace( 'øÆ', '', 'äÄøØæÆöÖüÜß', false ),
         'Assert 4 fails' );
   }

   public function testStripTags()
   {
      $this->assertEquals( 'Foo x', \Beluga\stripTags( '<b>Foo</b> x' ), 'Assert 1 fails' );
      $this->assertEquals( 'Foo', \Beluga\stripTags( '<!-- Blub --><strong>Foo</strong>' ), 'Assert 2 fails' );
      $this->assertEquals( 'Foo', \Beluga\stripTags( '<script>foo();</script>Foo' ), 'Assert 3 fails' );
   }

   public function testPrintH()
   {
      \ob_start();
      \Beluga\print_h( "Test\nFoo", 'cls' );
      $output = \ob_get_contents();
      \ob_end_clean();
      $this->assertEquals( "<pre class=\"cls\">Test\nFoo</pre>", $output, 'Assert 1 fails' );
      \ob_start();
      \Beluga\print_h( "&Test\nFoo", 'cls' );
      $output = \ob_get_contents();
      \ob_end_clean();
      $this->assertEquals( "<pre class=\"cls\">&amp;Test\nFoo</pre>", $output, 'Assert 1 fails' );
   }

   public function testSplitLines()
   {
      $this->assertEquals(
         [ '»«¢„“”', 'µ·…–ĸ', 'ŋđðſæ@ł–ĸŋ', '€¶ŧ←↓→øþ' ],
         \Beluga\splitLines( "»«¢„“”\nµ·…–ĸ\r\nŋđðſæ@ł–ĸŋ\r€¶ŧ←↓→øþ" ),
         'Assert 1 fails' );
   }

}

