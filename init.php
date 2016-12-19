<?php
/**
 * Copyright (c) 2016 AlexaCRM.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if ( !@require_once( __DIR__ . '/vendor/autoload.php' ) && !@require_once( __DIR__ . '/../../autoload.php' ) ) {
    spl_autoload_register( function( $className ) {
        $namespacePrefix = 'AlexaCRM\\CRMToolkit\\';

        $baseDirectory = __DIR__ . '/src/';

        $namespacePrefixLength = strlen( $namespacePrefix );
        if ( strncmp( $namespacePrefix, $className, $namespacePrefixLength ) !== 0 ) {
            return;
        }

        $relativeClassName = substr( $className, $namespacePrefixLength );

        $classFilename = $baseDirectory . str_replace( '\\', '/', $relativeClassName ) . '.php';

        if ( file_exists( $classFilename ) ) {
            require $classFilename;
        }
    } );
}
