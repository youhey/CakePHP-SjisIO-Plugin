<?php
/**
 * シフトJISの入出力対応プラグイン
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

/**
 * VIEWのレンダリング結果をシフトJISで出力する
 * 
 * <p>出力処理で「Layout」が無効になっていた場合は、出力を変換しません<br />
 * これは文字コードの変換処理を「afterLayout」で実装しているためです<br />
 * （フックできるViewの出力処理における終端が「afterLayout」だったから）<br />
 * また、Viewの処理で「Layout」有効／無効で処理ロジックが異なるため</p>
 * 
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class SjisOutputHelper extends AppHelper {

    /** シフトJISエンコーディング */
    const SJIS_ENCODING = 'SJIS-win';

    /**
     * ヘルパの設定
     *
     * - encoding  - 内部文字エンコーディング
     *
     * @var array
     **/
    private $settings = array('encoding' => null);

    /**
     * 内部文字エンコーディング
     *
     * @var string
     **/
    private $encoding = null;

    /**
     * コンストラクタ
     *
     * @param array $settings 設定
     */
    public function __construct($settings = array()) {
        $this->settings = am($this->settings, $settings);

        if (empty($this->settings['encoding'])) {
            $this->settings['encoding'] = Configure::read('App.encoding');
        }
        $this->encoding = $this->settings['encoding'];
    }

    /**
     * Layoutのレンダリング後に、出力の文字コードをシフトJISに変換
     * 
     * @return void
     */
    public function afterLayout()
    {
        $View = ClassRegistry::getObject('view');
        if (isset($View->output)) {
            $View->output = $this->toSjis($View->output);
        }
    }

    /**
     * 文字列をシフトJISに変換する
     * 
     * @param  string $output 変換する文字列
     * @return string シフトJISに変換した文字列
     */
    private function toSjis($output)
    {
        $to   = self::SJIS_ENCODING;
        $from = $this->encoding;
        $sjis = mb_convert_encoding($output, $to, $from);

        return $sjis;
    }
}
