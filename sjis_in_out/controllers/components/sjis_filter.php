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
 * シフトJISの入力を内部文字コードに変換するコンポーネント
 * 
 * <p>コントローラに設定することで、出力をシフトJISに変換します。<br />
 * また、受け取ったリクエストをシフトJISから内部文字コードに変換します。</p>
 * 
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class SjisFilterComponent extends Object {

    /** シフトJISエンコーディング */
    const SJIS_ENCODING = 'SJIS-win';

    /** 半角を全角に変換 */
    const HANKAKU_TO_ZENKAKU = 'KV';

    /** Content-Type */
    const 
        CONTENT_TYPE_HTML  = 'text/html', 
        CONTENT_TYPE_XHTML = 'application/xhtml+xml';

    /** Charset */
    const CONTENT_CHARSET = 'Shift_JIS';

    /**
     * コンポーネントの設定
     *
     * - encoding    - 内部文字エンコーディング
     * - contentType - Content-Type
     * - charset     - コンテンツのキャラクターセット
     * - logEnable   - TRUEであればデバッグ情報をログに書き込むか？
     *
     * @var array
     **/
    private $settings = array(
            'encoding'    => null,
            'contentType' => self::CONTENT_TYPE_HTML,
            'charset'     => self::CONTENT_CHARSET,
            'logEnable'   => false,
        );

    /**
     * 内部文字エンコーディング
     *
     * @var string
     **/
    private static $encoding = null;

    /**
     * コンポーネントを初期化
     * 
     * <p>コンポーネントの初期化設定にあわせて動作の設定を更新</p>
     * 
     * @param  Controller $controller コントローラ
     * @param  array $settings 設定
     * @return void
     * @link   http://book.cakephp.org/ja/view/64/Creating-Components
     */
    public function initialize(Controller $controller, $settings) {
        $this->settings = am($this->settings, $settings);

        if (empty($this->settings['encoding'])) {
            $this->settings['encoding'] = Configure::read('App.encoding');
        }
        self::$encoding = $this->settings['encoding'];

        self::convertInputToInternal($controller->params);
        self::convertHankakuToZenkaku($controller->params);

        if ($this->settings['logEnable']) {
            $message = "Character-code of the input value was converted: " 
                     . "from Shift_JIS into {self::$encoding }";
            $this->log($message, LOG_DEBUG);
        }
    }

    /**
     * コンポーネントを起動
     * 
     * <p>出力をシフトJISに変換するためのヘルパを動的に読み込む</p>
     * 
     * @param  Controller $controller コントローラ
     * @return void
     * @link   http://book.cakephp.org/ja/view/64/Creating-Components
     */
    public function startup(Controller $controller) {
        $additionHelpers = array(
                'SjisInOut.SjisOutput' => array(
                        'encoding' => self::$encoding, 
                    ),
            );
        $controller->helpers = Set::merge($controller->helpers, $additionHelpers);
    }

    /**
     * コントローラがビューをレンダリングする前にフック
     * 
     * <p>出力をシフトJISに変換するためのヘルパを動的に読み込む</p>
     * 
     * @param  Controller $controller コントローラ
     * @return void
     * @link   http://book.cakephp.org/ja/view/64/Creating-Components
     */
    public function beforeRender(Controller $controller) {
        $contentType = $this->contentType();
        $controller->header($contentType);
    }

    /**
     * Content-Typeを返却
     * 
     * @return string Content-Type
     */
    private function contentType() {
        $contentType = "Content-Type: {$this->settings['contentType']}";
        $charset     = $this->settings['charset'];
        if (!empty($charset)) {
            $contentType .= "; charset={$charset}";
        }

        return $contentType;
    }

    /**
     * 入力文字列の文字コードを内部文字コードに変換する
     * 
     * <p>シフトJISの文字列は参照で受け取る。<br />
     * 内部文字エンコーディングに変換した文字列で参照を更新する。</p>
     * <p>入力は配列やオブジェクトを考慮して再帰処理で対応する。<br />
     * 配列内部で保持する参照関係が切れて、変換の結果が伝播できない</p>
     * 
     * @param  mixed $value シフトJISの文字列
     * @return void
     */
    private static function convertInputToInternal(&$value) {
        if ($value !== null) {
            if (is_array($value) || is_object($value)) {
                array_walk_recursive($value, array(__CLASS__, __FUNCTION__));
            } elseif (is_string($value)) {
                $to    = self::$encoding;
                $from  = self::SJIS_ENCODING;
                $sjis  = mb_convert_encoding($value, $to, $from);
                $value = $sjis;
            }
        }
    }

    /**
     * 半角カタカナを全角カタカナに変換する
     * 
     * <p>半角カタカナを含む文字列は参照で受け取る。<br />
     * 全角カタカナに変換した文字列で参照を更新する。</p>
     * 
     * @param  mixed $value 半角カタカナを含む文字列
     * @return void
     */
    private static function convertHankakuToZenkaku(&$value) {
        if ($value !== null) {
            if (is_array($value) || is_object($value)) {
                array_walk_recursive($value, array(__CLASS__, __FUNCTION__));
            } elseif (is_string($value)) {
                $option   = self::HANKAKU_TO_ZENKAKU;
                $encoding = self::$encoding;
                $zenkaku  = mb_convert_kana($value, $option, $encoding);
                $value    = $zenkaku;
            }
        }
    }
}
