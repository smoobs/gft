#!/usr/bin/env perl

use v5.10;

use autodie;
use strict;
use warnings;

use Data::Dumper;
use Getopt::Long;
use PHP::Serialization qw( serialize unserialize );
use Scalar::Util qw( blessed );

use constant SYNTAX => <<EOS;
Syntax: $0 <from> <to> [<from> <to>...] < in.sql > out.sql
EOS

GetOptions() or die SYNTAX;
@ARGV & 1 && die SYNTAX;
fixup(@ARGV);

sub fixup {
  my %replace = @_;

  my %UNESC = (
    '\\0'  => "\0",
    '\\"'  => '"',
    "\\'"  => "'",
    '\\b'  => "\b",
    '\\n'  => "\n",
    '\\r'  => "\r",
    '\\t'  => "\t",
    '\\\\' => '\\',
  );

  my $escape   = mk_encoder( reverse %UNESC );
  my $unescape = mk_encoder(%UNESC);
  my $matcher  = mk_matcher( keys %replace );
  my $fixer    = mk_fixer( $matcher, %replace );
  my $vfixer   = mk_value_fixer( $matcher, $fixer, $escape, $unescape );

  while (<STDIN>) {
    chomp( my $ln = $_ );
    $ln =~ s/'((?:\\.|[^'\\]*)*)'/"'" . $vfixer->($1) . "'"/eg;
    print "$ln\n";
  }
}

sub mk_value_fixer {
  my ( $matcher, $fixer, $escape, $unescape ) = @_;
  return sub {
    my $v  = shift;
    my $vv = $unescape->($v);
    return $v unless $vv =~ $matcher;
    my $ds = eval { unserialize $vv };
    if ($@) {
      $vv = $fixer->($vv);
    }
    else {
      $vv = serialize( walk( $ds, $fixer ) );
    }

    return $escape->($vv);
  };
}

sub mk_fixer {
  my ( $matcher, %replace ) = @_;

  return sub {
    my $str = shift;
    $str =~ s/$matcher/$replace{$1}/eg;
    return $str;
  };
}

sub mk_encoder {
  my %kv = @_;
  my $m  = mk_matcher( keys %kv );
  return sub {
    my $str = shift;
    $str =~ s/$m/$kv{$1}/eg;
    return $str;
  };
}

sub mk_matcher {
  my $pat = join '|', map quotemeta, sort @_;
  return qr{($pat)};
}

sub unbless {
  my $obj = shift;
  return $obj unless defined $obj && blessed $obj;
  return {%$obj} if UNIVERSAL::isa( $obj, 'HASH' );
  return [@$obj] if UNIVERSAL::isa( $obj, 'ARRAY' );
  die;
}

sub walk {
  my ( $ds, $fixer ) = @_;
  return undef unless defined $ds;
  if ( my $pkg = blessed $ds ) {
    return bless walk( unbless($ds), $fixer ), $pkg;
  }
  if ( ref $ds ) {
    if ( 'HASH' eq ref $ds ) {
      return { map { $_ => walk( $ds->{$_}, $fixer ) } keys %$ds };
    }
    elsif ( 'ARRAY' eq ref $ds ) {
      return [map { walk( $_, $fixer ) } @$ds];
    }
    else { die Dumper($ds); }
  }
  else {
    return $fixer->($ds);
  }
}

# vim:ts=2:sw=2:sts=2:et:ft=perl

