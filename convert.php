<?php

/**
 * Converts email addresses characters to HTML entities to block spam bots.
 *
 * @since 0.71
 *
 * @param string $email_address Email address.
 * @param int    $hex_encoding  Optional. Set to 1 to enable hex encoding.
 * @return string Converted email address.
 */
function antispambot( $email_address, $hex_encoding = 0 ) {
  $email_no_spam_address = '';
  for ( $i = 0, $len = strlen( $email_address ); $i < $len; $i++ ) {
    $j = rand( 0, 1 + $hex_encoding );
    if ( 0 == $j ) {
      $email_no_spam_address .= '&#' . ord( $email_address[ $i ] ) . ';';
    } elseif ( 1 == $j ) {
      $email_no_spam_address .= $email_address[ $i ];
    } elseif ( 2 == $j ) {
      $email_no_spam_address .= '%' . zeroise( dechex( ord( $email_address[ $i ] ) ), 2 );
    }
  }

  return str_replace( '@', '&#64;', $email_no_spam_address );
}

echo antispambot('feedback@drupalnyc.org');
