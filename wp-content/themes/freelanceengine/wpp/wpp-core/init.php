<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'WPP_CORE', true );

require_once 'helpers/init.php';
require_once 'for-template.php';
require_once 'wpp-conditional-tags.php';
require_once 'wpp-conditional-tags.php';
require_once 'Wpp_Fr_Assets.php';
require_once 'Bfi_Thumb.php';
require_once 'extention/init.php';
require_once 'form/init.php';

require_once 'quill/Listener.php';
require_once 'quill/BlockListener.php';
require_once 'quill/Debug.php';
require_once 'quill/InlineListener.php';
require_once 'quill/Lexer.php';
require_once 'quill/Line.php';

require_once 'quill/Pick.php';

require_once 'quill/listener/Align.php';
require_once 'quill/listener/Blockquote.php';
require_once 'quill/listener/Bold.php';
require_once 'quill/listener/Color.php';
require_once 'quill/listener/Font.php';
require_once 'quill/listener/Heading.php';
require_once 'quill/listener/Image.php';
require_once 'quill/listener/Italic.php';
require_once 'quill/listener/Link.php';
require_once 'quill/listener/Lists.php';
require_once 'quill/listener/Mention.php';
require_once 'quill/listener/Script.php';
require_once 'quill/listener/Strike.php';
require_once 'quill/listener/Text.php';
require_once 'quill/listener/Underline.php';
require_once 'quill/listener/Video.php';


