<?php

$plugin_info = array(
    'pi_name'           => 'Compressifier',
    'pi_version'        => '0.1',
    'pi_author'         => 'James McFall',
    'pi_author_url'     => 'http://mcfall.geek.nz/',
    'pi_description'    => 'A simple plugin allowing you to combine selected CSS
                            and JS files into singular, minified files.',
    'pi_usage'          => null
);

class Compressifier {
    
    private $_tag_data = null;    
    private $_output_path = "/public/compressifier/";
    private $_dr = null;
    
    /**
     * The contructor
     */
    public function __construct() {
        $this->EE =& get_instance();
        $this->_dr = $_SERVER["DOCUMENT_ROOT"];
        $this->_tag_data = $this->EE->TMPL->tagdata;
        
        # Create the output direcotyr if it doesn't already exist
        $this->_create_output_dir();
        
        # Create the array of files, seperated by extension.
        $file_array = $this->_create_file_array();
        
        # Create the singular files for css/js
        $this->_create_files($file_array);
        
        $output_markup = '';
        if (array_key_exists("css", $file_array))
            $output_markup .= '<link rel="stylesheet" type="text/css" href="' . $this->_output_path . 'compressify.css" />';
        
        if (array_key_exists("js", $file_array))
            $output_markup .= '<script src="' . $this->_output_path . 'compressify.js"></script>';
        
        # Replace the contents of the tags with the script/link tags to the compressified files.
        $this->return_data = $output_markup;           
    }
    
    /**
     * Create the output directory the compressifier .js and .css files will be
     * located in.
     * 
     * @return <void>
     */
    private function _create_output_dir() {
        
        # Build the full OS path to the output dir
        $output_dir = $this->_dr . $this->_output_path;
        
        $dir = @mkdir($output_dir);
        
        # If it failed to create, throw an exception
        if (!$dir) {
            throw new Exception(
                    "Compressifier Exception: Failed to create output directory 
                     at " . $output_dir. ". Please create it manually."
                    );
        }
    }
    
    /**
     * Create a singular .js and a .css file with the contents of all of the 
     * files in between the tags.
     * 
     * @param <array> $file_array 
     * @return <void>
     */
    private function _create_files($file_array) {
        
        # Read each file contents into an array
        foreach ($file_array as $extension => $type_array) {
            
            # Create a singlular file for this extension
            $output_file = new SplFileObject($this->_dr . $this->_output_path . "compressify." . $extension, "a");
            
            # Write the contents of each of the other
            $contents = "";
            foreach ($type_array as $file) {
                $contents .= file_get_contents($this->_dr . $file);
            }
            $output_file->fwrite($contents);            
        }
    }
    
    
    
    
    
    /**
     * Build the file array
     * 
     * Take the tag input and convert it into an array of file paths, setting 
     * the extension as the key for each array item so we can create different
     * files for each file type.
     * 
     * i.e. $array['extension'] = $file_type_array;
     * 
     * @return <array>
     */
    private function _create_file_array() {
        
        $tmp_array = $tmp_file_array = array();
        
        # Format into a pipe delimited string of paths
        $tmp_file_string = preg_replace("/\s+/", "|", $this->_tag_data);
        $tmp_file_string = trim($tmp_file_string, "|");
        
        # Explode into an array
        $tmp_array = explode("|", $tmp_file_string);
        
        # Now format into an array in the format $array['extension'] = $file_type_array;
        foreach ($tmp_array as $file_path) {
            $tmp_file_array[$this->_get_extension($file_path)][] = $file_path;
        }
        
        return $tmp_file_array;
    }
    
    
    /**
     * Return a file extension when a file path is supplied. Otherwise false.
     * 
     * @param <string> $file_path
     * @return <boolean>|<string>
     */
    private function _get_extension($file_path) {
        
        $path_info = pathinfo($file_path);

        if (is_null($path_info))
            return false;
        
        return $path_info["extension"];
    }
}
?>
