<?php
/**
 * SJIS I/O Test
 * 
 * PHP versions >= 5.2
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @since   SjisIO 1.0
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::import('Controller', 'Controller', false);
App::import('Component', 'SjisInOut.SjisFilter');

class TestSjisFilterController extends Controller
{
    public 
        $uses = array(), 
        $params = array();
}

/** 
 * シフトJISの入力を内部文字コードに変換
 * 
 * @author IKEDA Youhei <ikeda@midc.jp>
 */
class SjisFilterComponentTest extends CakeTestCase
{

    public 
        $autoFixtures = false, 
        $fixtures     = array();

    private $encoding = null;

    public function startTest() {
        $this->encoding = Configure::read('App.encoding');
    }
    public function endTest() {
        ClassRegistry::flush();
    }

    public function test：シフトJISの入力を内部文字コードに変換するテスト() {
        $入力 = array(
                'foo'   => 'いろはにほへとちり', 
                'bar'   => 'イロハニホヘトチリ', 
                'hoge'  => '一十百千万億兆京垓', 
                '42'    => '４２', 
                'array' => array('わ', 'を', 'ん'), 
                'ascii' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', 
                'multi' => array('second' => array('third' => array('fourth' => array('fifth' => '美しい日本語')))), 
            );

        $シフトJISの入力 = $入力;
        mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);


        $controller = new TestSjisFilterController();
        $controller->params = $シフトJISの入力;

        $SjisFilter = new SjisFilterComponent();
        $SjisFilter->initialize($controller, array());
        $SjisFilter->startup($controller);

        $result   = $controller->params;
        $expected = $入力;
        $this->assertIdentical($expected, $result);
    }

    public function test：CP932の変換をテスト() {
        $NEC特殊文字 = array(
                '①', 
                '②', 
                '③', 
                '④', 
                '⑤', 
                '⑥', 
                '⑦', 
                '⑧', 
                '⑨', 
                '⑩', 
                '⑪', 
                '⑫', 
                '⑬', 
                '⑭', 
                '⑮', 
                '⑯', 
                '⑰', 
                '⑱', 
                '⑲', 
                '⑳', 
                'Ⅰ', 
                'Ⅱ', 
                'Ⅲ', 
                'Ⅳ', 
                'Ⅴ', 
                'Ⅵ', 
                'Ⅶ', 
                'Ⅷ', 
                'Ⅸ', 
                'Ⅹ', 
                '㍉', 
                '㌔', 
                '㌢', 
                '㍍', 
                '㌘', 
                '㌧', 
                '㌃', 
                '㌶', 
                '㍑', 
                '㍗', 
                '㌍', 
                '㌦', 
                '㌣', 
                '㌫', 
                '㍊', 
                '㌻', 
                '㎜', 
                '㎝', 
                '㎞', 
                '㎎', 
                '㎏', 
                '㏄', 
                '㎡', 
                '㍻', 
                '〝', 
                '〟', 
                '№', 
                '㏍', 
                '℡', 
                '㊤', 
                '㊥', 
                '㊦', 
                '㊧', 
                '㊨', 
                '㈱', 
                '㈲', 
                '㈹', 
                '㍾', 
                '㍽', 
                '㍼', 
                '≒', 
                '≡', 
                '∫', 
                '∮', 
                '√', 
                '⊥', 
                '∠', 
                '∟', 
                '⊿', 
                '∵', 
                '∩', 
                '∪', 
            );
        $IBM拡張文字 = array(
                '髙村薫', 
                // '內田百閒', /* 非対応 */
                '手塚治虫', 
                '德永英明', 
                '宮﨑あおい', 
                '草彅剛', 
                '里見弴', 
                '李承燁', 
                '鄭珉台', 
                '鄧小平', 
            );

        $入力要素 = array($NEC特殊文字, $IBM拡張文字);
        foreach ($入力要素 as $入力) {

            $シフトJISの入力 = $入力;
            mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);

            $controller = new TestSjisFilterController();
            $controller->params = $シフトJISの入力;

            $SjisFilter = new SjisFilterComponent();
            $SjisFilter->initialize($controller, array());
            $SjisFilter->startup($controller);

            $result   = $controller->params;
            $expected = $入力;
            $this->assertIdentical($expected, $result);

            $controller = null;
            $SjisFilter = null;
        }

    }

    public function test：半角カタカナを全角に変換するテスト() {
        $シフトJISの入力 = array(
                'ｧ', 
                'ｱ', 
                'ｨ', 
                'ｲ', 
                'ｩ', 
                'ｳ', 
                'ｪ', 
                'ｴ', 
                'ｫ', 
                'ｵ', 
                'ｶ', 
                'ｶﾞ', 
                'ｷ', 
                'ｷﾞ', 
                'ｸ', 
                'ｸﾞ', 
                'ｹ', 
                'ｹﾞ', 
                'ｺ', 
                'ｺﾞ', 
                'ｻ', 
                'ｻﾞ', 
                'ｼ', 
                'ｼﾞ', 
                'ｽ', 
                'ｽﾞ', 
                'ｾ', 
                'ｾﾞ', 
                'ｿ', 
                'ｿﾞ', 
                'ﾀ', 
                'ﾀﾞ', 
                'ﾁ', 
                'ﾁﾞ', 
                'ｯ', 
                'ﾂ', 
                'ﾂﾞ', 
                'ﾃ', 
                'ﾃﾞ', 
                'ﾄ', 
                'ﾄﾞ', 
                'ﾅ', 
                'ﾆ', 
                'ﾇ', 
                'ﾈ', 
                'ﾉ', 
                'ﾊ', 
                'ﾊﾞ', 
                'ﾊﾟ', 
                'ﾋ', 
                'ﾋﾞ', 
                'ﾋﾟ', 
                'ﾌ', 
                'ﾌﾞ', 
                'ﾌﾟ', 
                'ﾍ', 
                'ﾍﾞ', 
                'ﾍﾟ', 
                'ﾎ', 
                'ﾎﾞ', 
                'ﾎﾟ', 
                'ﾏ', 
                'ﾐ', 
                'ﾑ', 
                'ﾒ', 
                'ﾓ', 
                'ｬ', 
                'ﾔ', 
                'ｭ', 
                'ﾕ', 
                'ｮ', 
                'ﾖ', 
                'ﾗ', 
                'ﾘ', 
                'ﾙ', 
                'ﾚ', 
                'ﾛ', 
                'ﾜ', 
                'ｦ', 
                'ﾝ', 
                'ｳﾞ', 
            );
        mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);


        $controller = new TestSjisFilterController();
        $controller->params = $シフトJISの入力;

        $SjisFilter = new SjisFilterComponent();
        $SjisFilter->initialize($controller, array());
        $SjisFilter->startup($controller);

        $result   = $controller->params;
        $expected = array(
                'ァ', 
                'ア', 
                'ィ', 
                'イ', 
                'ゥ', 
                'ウ', 
                'ェ', 
                'エ', 
                'ォ', 
                'オ', 
                'カ', 
                'ガ', 
                'キ', 
                'ギ', 
                'ク', 
                'グ', 
                'ケ', 
                'ゲ', 
                'コ', 
                'ゴ', 
                'サ', 
                'ザ', 
                'シ', 
                'ジ', 
                'ス', 
                'ズ', 
                'セ', 
                'ゼ', 
                'ソ', 
                'ゾ', 
                'タ', 
                'ダ', 
                'チ', 
                'ヂ', 
                'ッ', 
                'ツ', 
                'ヅ', 
                'テ', 
                'デ', 
                'ト', 
                'ド', 
                'ナ', 
                'ニ', 
                'ヌ', 
                'ネ', 
                'ノ', 
                'ハ', 
                'バ', 
                'パ', 
                'ヒ', 
                'ビ', 
                'ピ', 
                'フ', 
                'ブ', 
                'プ', 
                'ヘ', 
                'ベ', 
                'ペ', 
                'ホ', 
                'ボ', 
                'ポ', 
                'マ', 
                'ミ', 
                'ム', 
                'メ', 
                'モ', 
                'ャ', 
                'ヤ', 
                'ュ', 
                'ユ', 
                'ョ', 
                'ヨ', 
                'ラ', 
                'リ', 
                'ル', 
                'レ', 
                'ロ', 
                'ワ', 
                'ヲ', 
                'ン', 
                'ヴ', 
            );
        $this->assertIdentical($expected, $result);
    }

    public function test：シフトJISで2バイトに意味がある文字をテスト() {
        $２バイト目が40 = array(
                'ァ', 
                'А', 
                '院', 
                '魁', 
                '機', 
                '掘', 
                '后', 
                '察', 
                '宗', 
                '拭', 
                '繊', 
                '叩', 
                '邸', 
                '如', 
                '鼻', 
                '法', 
                '諭', 
                '蓮', 
                '僉', 
                '咫', 
                '奸', 
                '廖', 
                '戞', 
                '曄', 
                '檗', 
                '漾', 
                '瓠', 
                '磧', 
                '紂', 
                '隋', 
                '蕁', 
                '襦', 
                '蹇', 
                '錙', 
                '顱', 
                '鵝', 
                '纊', 
                '犾', 
                '涖', 
                '髜'
            );
        $２バイト目が5B = array(
                'ー', 
                'ゼ', 
                'Ъ', 
                '閏', 
                '骸', 
                '擬', 
                '啓', 
                '梗', 
                '纂', 
                '充', 
                '深', 
                '措', 
                '端', 
                '甜', 
                '納', 
                '票', 
                '房', 
                '夕', 
                '麓', 
                '兌', 
                '喙', 
                '媼', 
                '彈', 
                '拏', 
                '杣', 
                '歇', 
                '濕', 
                '畆', 
                '禺', 
                '綣', 
                '膽', 
                '藜', 
                '觴', 
                '躰', 
                '鐚', 
                '饉', 
                '鷦', 
                '倞', 
                '劯', 
                '∵', 
                '犱'
            );
        $２バイト目が5C = array(
                '―', 
                'ソ', 
                'Ы', 
                '噂', 
                '浬', 
                '欺', 
                '圭', 
                '構', 
                '蚕', 
                '十', 
                '申', 
                '曾', 
                '箪', 
                '貼', 
                '能', 
                '表', 
                '暴', 
                '予', 
                '禄', 
                '兔', 
                '喀', 
                '媾', 
                '彌', 
                '拿', 
                '杤', 
                '歃', 
                '濬', 
                '畚', 
                '秉', 
                '綵', 
                '臀', 
                '藹', 
                '觸', 
                '軆', 
                '鐔', 
                '饅', 
                '鷭', 
                '偆', 
                '砡', 
                '纊', 
                '犾'
            );
        $２バイト目が5D = array(
                '‐', 
                'ゾ', 
                'Ь', 
                '云', 
                '馨', 
                '犠', 
                '珪', 
                '江', 
                '讃', 
                '従', 
                '疹', 
                '曽', 
                '綻', 
                '転', 
                '脳', 
                '評', 
                '望', 
                '余', 
                '肋', 
                '兢', 
                '咯', 
                '嫋', 
                '彎', 
                '拆', 
                '枉', 
                '歉', 
                '濔', 
                '畩', 
                '秕', 
                '緇', 
                '臂', 
                '蘊', 
                '訃', 
                '躱', 
                '鐓', 
                '饐', 
                '鷯', 
                '偰', 
                '硎', 
                '褜', 
                '猤'
            );
        $２バイト目が5E = array(
                '／', 
                'タ', 
                'Э', 
                '運', 
                '蛙', 
                '疑', 
                '型', 
                '洪', 
                '賛', 
                '戎', 
                '真', 
                '楚', 
                '耽', 
                '顛', 
                '膿', 
                '豹', 
                '某', 
                '与', 
                '録', 
                '竸', 
                '喊', 
                '嫂', 
                '弯', 
                '擔', 
                '杰', 
                '歐', 
                '濘', 
                '畤', 
                '秧', 
                '綽', 
                '膺', 
                '蘓', 
                '訖', 
                '躾', 
                '鐃', 
                '饋', 
                '鷽', 
                '偂', 
                '硤', 
                '鍈'
            );
        $２バイト目が5F = array(
                '＼', 
                'ダ', 
                'Ю', 
                '雲', 
                '垣', 
                '祇', 
                '契', 
                '浩', 
                '酸', 
                '柔', 
                '神', 
                '狙', 
                '胆', 
                '点', 
                '農', 
                '廟', 
                '棒', 
                '誉', 
                '論', 
                '兩', 
                '喟', 
                '媽', 
                '彑', 
                '拈', 
                '枩', 
                '歙', 
                '濱', 
                '畧', 
                '秬', 
                '綫', 
                '臉', 
                '蘋', 
                '訐', 
                '軅', 
                '鐇', 
                '饑', 
                '鸚', 
                '傔', 
                '硺', 
                '銈', 
                '獷'
            );
        $２バイト目が60 = array(
                'Ａ', 
                'チ', 
                'Я', 
                '荏', 
                '柿', 
                '義', 
                '形', 
                '港', 
                '餐', 
                '汁', 
                '秦', 
                '疏', 
                '蛋', 
                '伝', 
                '覗', 
                '描', 
                '冒', 
                '輿', 
                '倭', 
                '兪', 
                '啻', 
                '嫣', 
                '彖', 
                '拜', 
                '杼', 
                '歔', 
                '濮', 
                '畫', 
                '秡', 
                '總', 
                '臍', 
                '藾', 
                '訌', 
                '軈', 
                '鐐', 
                '饒', 
                '鸛', 
                '蓜', 
                '玽'
            );
        $２バイト目が7B = array(
                '＋', 
                'ボ', 
                'к', 
                '閲', 
                '顎', 
                '宮', 
                '鶏', 
                '砿', 
                '施', 
                '旬', 
                '須', 
                '捜', 
                '畜', 
                '怒', 
                '倍', 
                '府', 
                '本', 
                '養', 
                '几', 
                '嘴', 
                '學', 
                '悳', 
                '掉', 
                '桀', 
                '毬', 
                '炮', 
                '痣', 
                '窖', 
                '縵', 
                '艝', 
                '蛔', 
                '諚', 
                '轆', 
                '閔', 
                '驅', 
                '黠', 
                '垬', 
                '葈', 
                '傔', 
                '硺'
            );
        $２バイト目が7C = array(
                'ポ', 
                'л', 
                '榎', 
                '掛', 
                '弓', 
                '芸', 
                '鋼', 
                '旨', 
                '楯', 
                '酢', 
                '掃', 
                '竹', 
                '倒', 
                '培', 
                '怖', 
                '翻', 
                '慾', 
                '處', 
                '嘶', 
                '斈', 
                '忿', 
                '掟', 
                '桍', 
                '毫', 
                '烟', 
                '痞', 
                '窩', 
                '縹', 
                '艚', 
                '蛞', 
                '諫', 
                '轎', 
                '閖', 
                '驂', 
                '黥', 
                '埈', 
                '蒴'
            );
        $２バイト目が7D = array(
                '±', 
                'マ', 
                'м', 
                '厭', 
                '笠', 
                '急', 
                '迎', 
                '閤', 
                '枝', 
                '殉', 
                '図', 
                '挿', 
                '筑', 
                '党', 
                '媒', 
                '扶', 
                '凡', 
                '抑', 
                '凩', 
                '嘲', 
                '孺', 
                '怡', 
                '掵', 
                '栲', 
                '毳', 
                '烋', 
                '痾', 
                '竈', 
                '繃', 
                '艟', 
                '蛩', 
                '諳', 
                '轗', 
                '閘', 
                '驀', 
                '黨', 
                '埇', 
                '蕓', 
                '僘'
            );
        $２バイト目が7E = array(
                '×', 
                'ミ', 
                'н', 
                '円', 
                '樫', 
                '救', 
                '鯨', 
                '降', 
                '止', 
                '淳', 
                '厨', 
                '掻', 
                '蓄', 
                '冬', 
                '梅', 
                '敷', 
                '盆', 
                '欲', 
                '凭', 
                '嘸', 
                '宀', 
                '恠', 
                '捫', 
                '桎', 
                '毯', 
                '烝', 
                '痿', 
                '窰', 
                '縷', 
                '艤', 
                '蛬', 
                '諧', 
                '轜', 
                '閙', 
                '驃', 
                '黯', 
                '蕙', 
                '兊'
            );

        $入力要素 = array(
                $２バイト目が40, 
                $２バイト目が5B, 
                $２バイト目が5C, 
                $２バイト目が5D, 
                $２バイト目が5E, 
                $２バイト目が5F, 
                $２バイト目が60, 
                $２バイト目が7B, 
                $２バイト目が7C, 
                $２バイト目が7D, 
                $２バイト目が7E, 
            );
        foreach ($入力要素 as $入力) {
            $シフトJISの入力 = $入力;
            mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);

            $controller = new TestSjisFilterController();
            $controller->params = $シフトJISの入力;

            $SjisFilter = new SjisFilterComponent();
            $SjisFilter->initialize($controller, array());
            $SjisFilter->startup($controller);

            $result   = $controller->params;
            $expected = $入力;
            $this->assertIdentical($expected, $result);

            $controller = null;
            $SjisFilter = null;
        }
    }

    public function test：シフトJISの入力をeucJPに変換するテスト() {
        $入力 = array(
                'foo'   => 'いろはにほへとちり', 
                'bar'   => 'イロハニホヘトチリ', 
                'hoge'  => '一十百千万億兆京垓', 
                '42'    => '４２', 
                'array' => array('わ', 'を', 'ん'), 
                'ascii' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', 
                'multi' => array('second' => array('third' => array('fourth' => array('fifth' => '美しい日本語')))), 
            );

        $シフトJISの入力 = $eucJPの入力 = $入力;
        mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);
        mb_convert_variables('eucJP-win', $this->encoding, $eucJPの入力);

        $controller = new TestSjisFilterController();
        $controller->params = $シフトJISの入力;

        $SjisFilter = new SjisFilterComponent();
        $SjisFilter->initialize($controller, array('encoding' => 'eucJP-win'));
        $SjisFilter->startup($controller);

        $result   = $controller->params;
        $expected = $eucJPの入力;
        $this->assertIdentical($expected, $result);
    }

    public function test：コントローラのコンポーネント使用をテスト() {
        $入力 = array(
                'foo'   => 'いろはにほへとちり', 
                'bar'   => 'イロハニホヘトチリ', 
                'hoge'  => '一十百千万億兆京垓', 
                '42'    => '４２', 
                'array' => array('わ', 'を', 'ん'), 
                'ascii' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', 
                'multi' => array('second' => array('third' => array('fourth' => array('fifth' => '美しい日本語')))), 
            );

        $シフトJISの入力 = $入力;
        mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);

        $Controller = new Controller;
        $Controller->params = $シフトJISの入力;
        $Controller->uses = array();
        $Controller->components = array('SjisInOut.SjisFilter');
        $Controller->constructClasses();
        $Controller->startupProcess();

        $result   = $Controller->params;
        $expected = $入力;
        $this->assertIdentical($expected, $result);
    }

    public function test：コントローラのデータ構造（参照関係）をテスト() {
        $入力 = array(
                '1st' => 'Foo Bar', 
                '2nd' => 'ほげ　ピヨ　ｆｕｇａ', 
                '3rd' => '山田　太郎', 
                '4th' => '株式会社MIDC', 
                '5th' => '４２', 
                '6th' => array('い', 'ろ', 'は'), 
                '7th' => array(array('入れ子')), 
            );

        $シフトJISの入力 = $入力;
        mb_convert_variables('SJIS-win', $this->encoding, $シフトJISの入力);

        $controller = new TestSjisFilterController();
        $controller->params = array(
                'data' => &$シフトJISの入力
            );
        $controller->data = &$controller->params['data'];

        $SjisFilter = new SjisFilterComponent();
        $SjisFilter->initialize($controller, array());
        $SjisFilter->startup($controller);

        $result   = $controller->params['data'];
        $expected = $入力;
        $this->assertIdentical($expected, $result, '文字コードの変換を確認');
        $this->assertIdentical($controller->params['data'], $controller->data, '参照関係を確認');
    }
}
