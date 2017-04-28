# General Information

GitHub Release: 
[![GitHub release](https://img.shields.io/github/release/qubyte/rubidium.svg)](https://github.com/sinri/enoch/releases)

Stable Release Version on Packagist:
[![Packagist](https://img.shields.io/packagist/v/sinri/enoch.svg)](https://packagist.org/packages/sinri/enoch)

# Changing Note

## 1.1.0

Router added support for Method and Pure Text judgement.

## 1.0.2

Fix https://github.com/sinri/enoch/issues/1

## 1.0.0

Make classes (`Spirit`, `LibSession` etc.) able to be constructed outside protected area.
Make method `sessionStart` of `LibSession` as deprecated; this could be called within `Lamech`.
Add a new linked-style call design to `LibMail` to send mail; and make method `sendMail` deprecated.
Make method parameter names as camelCase-style.
Add method `safeReadArray` to `Spirit`.
Add property `spirit` as instance of `Spirit` to `Lamech` and `Nammah`.

## 0.9.9

It is the base and the last Framework Research Version.
