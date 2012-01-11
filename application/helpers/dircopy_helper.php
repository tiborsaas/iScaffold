<?php

	/**
	 *	From
	 * 	http://www.php.net/manual/en/function.copy.php#103732
	 */

    define('DS', DIRECTORY_SEPARATOR); // I always use this short form in my code.

    function dircopy( $path, $dest )
    {
        if( is_dir( $path ) )
        {
            @mkdir( $dest );
            $objects = scandir( $path );
            if( sizeof( $objects) > 0 )
            {
                foreach( $objects as $file )
                {
                    if( $file == "." || $file == ".." )
                        continue;
                    // go on
                    if( is_dir( $path.DS.$file ) )
                    {
                        dircopy( $path.DS.$file, $dest.DS.$file );
                    }
                    else
                    {
                        copy( $path.DS.$file, $dest.DS.$file );
                    }
                }
            }
            return true;
        }
        elseif( is_file( $path ) )
        {
            return copy( $path, $dest );
        }
        else
        {
            return false;
        }
    }