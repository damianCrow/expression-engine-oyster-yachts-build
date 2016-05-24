# [Link Vault](http://masugadesign.com/software/link-vault)

If you need a simple way to mask links, this is it.

[Link Vault on devot:ee](http://devot-ee.com/add-ons/link-vault)

[Documentation](http://masugadesign.com/software/link-vault/tags)

[Changelog](http://masugadesign.com/software/link-vault/changelog)

## Requirements

ExpressionEngine 2.7+

## Tags

### :download_link

#### Parameters

##### file_name
The file name (commonly used with directory="")
##### directory
Enter a directory path if the file is not in the default hidden folder. (commonly used with file_name="")
##### file_path
If you don't specify a file_name and directory independently, use this parameter to specify the complete path or a URL that contains the true path to the file. Note: Do not use a URL for S3 links; only use folder path and file name.
##### download_as
To override the actual file name for the download, enter a valid file name in this parameter without the file extension. This parameter is only available when serving a file stored locally on the server.
##### entry_id
An entry ID may be specified for logging purposes if download files are associated with a channel.
##### url_only
If value is 'true', only the URL will be returned. The default is 'false'.
##### action_only
If value is 'true', the URL will exclude the site index from the beginning of the URL. Only the action number and the rest of the query string will be included in the returned URL.
##### class
CSS class for the link's style
##### text
The text that is displayed in the link. The default is 'Download'.
##### remote
Set this to "true" if the file is hosted on a remote server. The default is "false".
##### cf:
'cf:' is the prefix for any custom field created in the Link Vault control panel. (Examples: cf:user_agent, cf:download_page, cf:screen_name)
##### s3_bucket
If you'd like to fetch a file from your Amazon S3 account, specify the bucket here. To specify which file you'd like to fetch, use the 'file_path' parameter. Note: You may need to override the default S3 endpoint using the 'link_vault_aws_endpoint' config variable if your bucket exists outside the US Standard Region. List of region endpoints
##### expires
Use this parameter to set an expiration date/time that the link will no longer show on the page upon page load. The date/time format must conform to one of the accepted PHP date/time formats.
##### expires_text
Use this parameter to specify some content that should be displayed if the link has expired.
##### show_file_name
Set this parameter to 'true' if you'd like the name of the file to appear in the generated URL. The query string parameter value created by this tag parameter is not used during processing.

#### Regular examples
```
{exp:link_vault:download_link file_name="file.zip" entry_id="520" member_id="23"}
{exp:link_vault:download_link file_name="file.zip" url_only="true"}
{exp:link_vault:download_link file_name="file.zip" cf:category="catname"}
{exp:link_vault:download_link file_name="file.zip" url_only="true" cf:category="catname"}
{exp:link_vault:download_link file_name="http://othersite.com/somefile.zip" remote="true" }
{exp:link_vault:download_link file_name="file.zip" expires="2013-01-01" expires_text="Not available" }
{exp:link_vault:download_link file_name="fonts.zip" show_file_name="true" }
{exp:link_vault:download_link file_path='/the/full/system/path/to/file.zip' text='Download now' }
{exp:link_vault:download_link directory='/the/full/system/path/to/' file_name='file.zip' text='Click this' }
{exp:link_vault:download_link directory='relative/to/docroot/' file_name='yes.mp3' text='Download song' }
{exp:link_vault:download_link file_name='super-big-directory-of-people.csv' as='directory' }
```

#### Amazon S3 Examples
Link Vault can retrieve files from your Amazon S3 account once you set your AWS access and secret keys
on the Link Vault settings screen in the EE control panel.  **Note: You may need to override the default S3 endpoint using the 'link_vault_aws_endpoint' config variable if your bucket exists outside the US Standard Region. [List of region endpoints](http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region)**
```
{exp:link_vault:download_link s3_bucket="downloadables" file_path="my/files/hidden.zip" }
{exp:link_vault:download_link s3_bucket="images" file_path="cutekitten.png" text="Download Kitty" }
```

### :download_url

This template tag is a shortcut for the **:download_link** tag with the **url_only** parameter set to **true**.

### :download_action

This template tag is a shortcut for the **:download_link** tag with the **url_only** and **action_only** parameters set to **true**.

### :download_button

#### Parameters

##### file_name
The file name (required)
##### directory
Enter a directory path if the file is not in the default hidden folder.
##### file_path
If you don't specify a file_name and directory independently, use this parameter to specify the complete path or a URL that contains the true path to the file. Note: Do not use a URL for S3 links; only use folder path and file name.
##### download_as
To override the actual file name for the download, enter a valid file name in this parameter without the file extension. This parameter is only available when serving a file stored locally on the server.
##### entry_id
An entry ID may be specified for logging purposes if download files are associated with a channel.
##### action_only
If value is 'true', the URL will exclude the site index from the beginning of the URL. Only the action number and the rest of the query string will be included in the returned URL.
##### class
CSS class for the button's style
##### text
The text that is displayed on the button. The default is 'Download'.
##### remote
Set this to "true" if the file is hosted on a remote server. The default is "false".
##### cf:
'cf:' is the prefix for any custom field created in the Link Vault control panel. (Examples: cf:user_agent, cf:download_page, cf:screen_name)
##### s3_bucket
If you'd like to fetch a file from your Amazon S3 account, specify the bucket here. To specify which file you'd like to fetch, use the 'file_path' parameter. Note: You may need to override the default S3 endpoint using the 'link_vault_aws_endpoint' config variable if your bucket exists outside the US Standard Region. List of region endpoints
##### expires
Use this parameter to set an expiration date/time that the button will no longer show on the page upon page load. The date/time format must conform to one of the accepted PHP date/time formats.
##### expires_text
Use this parameter to specify some content that should be displayed if the button has expired.

#### Regular Examples
```
{exp:link_vault:download_button file_name="http://www.someothersite.com/file.zip"}
{exp:link_vault:download_button entry_id="1234" file_name="http://www.someothersite.com/file.zip"}
{exp:link_vault:download_button file_path="http://www.someothersite.com/file.zip" remote="true" class="big_green" text="Download our App"}
```

#### Amazon S3 Examples
Link Vault can retrieve files from your Amazon S3 account once you set your AWS access and secret keys
on the Link Vault settings screen in the EE control panel.
```
{exp:link_vault:download_button s3_bucket="videos" file_path="flvs/ee_fieldtype_dev.flv" }
{exp:link_vault:download_button s3_bucket="peppers" file_path="jalapenos.jpg" }
```

### :download_count
```
{exp:link_vault:download_count entry_id="1234"}
{exp:link_vault:download_count file_name="file.zip"}
{exp:link_vault:download_count file_name="file.zip" member_id="5"}
{exp:link_vault:download_count file_name="protected_file.zip" table_name="leeches"}
```

### :file_size
```
{exp:link_vault:file_size file_name="file.zip" directory="downloads/zips/"}
{exp:link_vault:file_size file_path="downloads/zips/file.zip"}
```

### :click_count
```
{exp:link_vault:click_count url="http://myothersite.com" }
{exp:link_vault:click_count url="http://myothersite.com" member_id="1209" start_date="2012-05-01" }
```

### :url
```
# A basic example
{exp:link_vault:url url="http://somesite.com/page" }

# An example with Link Vault custom fields
{exp:link_vault:url url="http://somesite.com/page" cf:screen_name="{screen_name}" cf:page="{segment_1}/{segment_2}"}
```

### :pretty_url
```
# Input
{exp:link_vault:pretty_url url='http://website.com' text='Visit my site'}
# Output
http://site.com?go=Visit-my-site&ACT=50
```

### :records

The :records template tag pair allows you to query the link_vault_downloads table or the link_vault_leeches table based on the parameters you specify. If you use the 'group_by' parameter, the 'census' count variable will also be available to use in the tag data. You can specify a custom name for the count variable by using the count_variable parameter.

##### All downloads with a specific value in a Link Vault custom field
```
{exp:link_vault:records table="downloads" cf:screen_name="Ben Kohl" variable_prefix="lv_"}
	{if lv_no_results}<p>No downloads for that user.</p>{/if}
	<p>{lv_cf_screen_name} downloaded {lv_file_name} on {lv_unix_time format="%Y-%m-%d"}.</p>
{/exp:link_vault:records}
```

##### All leech attempts for a specific file
```
{exp:link_vault:records table="leeches" file_name="britneyspears.png" }
	<p>{member_id} attempted to copy a download link on {unix_time format="%m/%d/%Y"}</p>
{/exp:link_vault:records}
```

##### All downloads by member with ID of 5 between the 1st and 5th of January
```
{exp:link_vault:records table="downloads" member_id="5" start_date="2013-1-1" end_date="2013-1-5" }
	<p>{file_name} was downloaded {unix_time format="%m/%d/%Y"}</p>
{/exp:link_vault:records}
```

##### Top ten downloaded files (default count_variable)
```
{exp:link_vault:records table="downloads" group_by="file_name" order_by="census" sort="desc" limit="10"}
	<p>{file_name} file has been downloaded {census} times.
{/exp:link_vault:records}
```

##### Top five downloaders (custom count_variable)
```
{exp:link_vault:records table="downloads" group_by="member_id" count_variable="my_count" order_by="my_count" sort="desc" limit="5"}
	<p>Member {member_id} has downloaded {my_count} files.</p>
{/exp:link_vault:records}
```

##### Top ten encrypted URL link clicks
```
{exp:link_vault:records table="link_clicks" group_by="url" order_by="census" sort="desc" limit="10"}
	<p>{count}. {url} - {census} clicks.</p>
{/exp:link_vault:records}
```

## Config Variables

### Config Variable Overrides
```
$config['link_vault_salt']            = '3j4h5j6kd';
$config['link_vault_hidden_folder']   = '/local/files/';
$config['link_vault_leech_url']       = 'http://mysite.com/no-leeching';
$config['link_vault_missing_url']     = 'http://mysite.com/missing-file';
$config['link_vault_block_leeching']  = 1;
$config['link_vault_log_leeching']    = 0;
$config['link_vault_log_link_clicks'] = 1;
$config['link_vault_debug']           = TRUE;
$config['link_vault_aws_access_key']  = 'SJGJ29405GKWLCM595830203FFG';
$config['link_vault_aws_secret_key']  = '3jfkf39459tkdksmcnd4j58t7djhsh2g2b4jfk';
$config['link_vault_aws_endpoint']    = 's3-us-west-2.amazonaws.com';

/*
This setting overrides the default timeout (5 seconds) that is set for
authenticated S3 download URLs. The value should be in seconds. It may be
desireable to increase this value to support downloads from devices that
request files in multiple byte range segments.
*/
$config['link_vault_s3_timeout']      = 5;

/*
Setting this to TRUE will exclude the content disposition response header
for S3 authenticated URL redirects. This results in images, pdfs and some
other files being presented in the browser rather than being downloaded.
*/
$config['link_vault_s3_exclude_response_header'] = TRUE;
```

## Extension Hooks

### link_vault_download_start
This hook is called immediately before a file is downloaded. The only parameter is the array of record data which can be manipulated within your extension but your method MUST return the record array.

**$log_record_data = ee()->extensions->call('link_vault_download_start', $log_record_data);**

### link_vault_download_end
This hook is called immediately after a file is downloaded and the download is logged.

**ee()->extensions->call('link_vault_download_end', $log_record_data, $log_id);**

### link_vault_remote_download_start
This hook is called immediately before a remote download attempt is logged and the user's browser attempts to download the file. This hook accepts the record data array as a parameter. You can manipulate the record data in your extension and your hook method MUST return the record data array upon completion.

**$log_record_data = ee()->extensions->call('link_vault_remote_download_start', $log_record_data);**

### link_vault_s3_download_start
This hook is called immediately before redirecting to a secure S3 URL. The hook method must return the $log_record_data.

**$log_record_data = ee()->extensions->cal('link_vault_s3_download_start', $log_record_data);**

### link_vault_log_leech_start
This hook is called immediately before logging a leech attempt. The hook method must return the $record_data array.

**$record_data = ee()->extensions->call('link_vault_log_leech_start', $record_data);**

### link_vault_log_leech_end
This hook is called immediately after logging a leech attempt before the module checks to see if file leeching is allowed. Any value returned by the hook is not used in the module.

**ee()->extensions->call('link_vault_log_leech_end', $record_data, $log_id);**

### link_vault_link_click_start
This hook is called immediately before logging a link click. You can use this hook to modify the data that will be stored in the database row or manually populate a custom field. The record data array must be returned at the end of the hook method.

**$record_data = ee()->extensions->call('link_vault_link_click_start', $record_data);**

## PHP Library
In order to use the Link Vault PHP library, the Link Vault add-on must be installed. To load the Link Vault library from another add-on, use this code:
```
ee()->load->add_package_path( PATH_THIRD.'link_vault' );
ee()->load->library('link_vault_library');
```

### url( $params=array(), $custom_field_params=array() )
##### $params (array)
The array of Link Vault parameters.
##### $custom_field_params (array)
The array of custom field parameters. They should be formatted like "cf_custom_field" rather than "cf:custom_field".
##### RETURN (string)
The masked URL.
```
$params = array(
	'url'		=> 'http://myothersite.com/some/page',
	'entry_id'	=> '340'
);
$custom_fields = array(
	'screen_name'	=> 'SuperDuperDev',
	'page_load'		=> '2013-08-13 04:23:44'
);
$url = ee()->link_vault_library->url($params, $custom_fields);
```

The encrypted link will look something like (abbreviated):
```
http://site.com?ACT=50&lv=yEmhRRj%2FbyVAHMOc0j76ko0ovNKiyKb8yoiewoX58jJupUeArbC8kuHjOzoDkYceeTSJ7k5FF9342pgjn
```

### pretty_url( $params=array(), $custom_field_params=array() )
**RETURN** : String - The pretty URL.
```
$params = array(
	'url'	=> 'http://somesite.com/probablyareallylonganduglyURLbutyouwanttotrackitanyway',
	'text'	=> 'Visit my other site'
);
$url = ee()->link_vault_library->pretty_url( $params );
```
The pretty URL will look something like this:
```
http://site.com?go=Visit-my-other-site&ACT=56
```

### download_link( $params=array(), $custom_field_params=array() )
**RETURN** : String - HTML anchor tag.
```
$link = ee()->link_vault_library->download_link(array(
	'directory'	=> '../the-files/',
	'file_name'	=> 'software.zip',
	'link_text'	=> 'Download my free software',
	'entry_id'	=> $entry_id
), array(
	'screen_name'	=> ee()->session->userdata('screen_name'),
));
```

### download_button( $params=array(), $custom_field_params=array() )
**RETURN** : String - HTML form and form elements.
```
$link = ee()->link_vault_library->download_button(array(
	'remote'		=> true,
	'file_name'		=> 'http://site.com/link/to/file/on/other/site/file.zip',
	'button_text'	=> 'Click here to download the zip file',
	'entry_id'		=> $entry_id
), array(
	'screen_name'	=> ee()->session->userdata('screen_name'),
));
```

### download_count( $params=array(), $custom_field_params=array() )
**RETURN** : Integer - The number of downloads matching the criteria specified in the parameters.
```
// Specifying a full system path and a member ID
$count = ee()->link_vault_library->download_count(array(
	'file_path'		=> '/the/system/path/to/the/file.zip',
	'member_id'		=> '312',
));

// Specifying a directory relative to the document root and file separately
$count = ee()->link_vault_library->download_count(array(
	'directory'		=> '../files/',
	'file_name'		=> 'music-collection.zip',
));

// Specifying a URL to a file that is hosted on the site
$count = ee()->link_vault_library->download_count(array(
	'file_path'		=> 'http://example.com/downloads/images/horsemask.jpg'
));
```

### click_count( $params=array(), $custom_field_params=array() )
**RETURN** : Integer - The number of times a link to a non-file URL has been clicked.
```
// Specifying a URL
$count = ee()->link_vault_library->click_count(array(
	'url'	=> 'http://example.com/watch/a/tutorial'
));

// Specifying a pretty URL ID
$count = ee()->link_vault_library->click_count(array(
	'pretty_url_id'	=> 3
));

// Counting link clicks between two dates for a particular member
$count = ee()->link_vault_library->click_count(array(
	'url'			=> 'http://example.com/charts/signups',
	'member_id'		=> '14023',
	'start_date'	=> '2013-01-01',
	'end_date'		=> '2013-04-15'
));
```

### file_size( $params=array() )
The **$params** array parameter may contain *file_path*, *directory* and/or *file_name*. If *file_path* and *directory*
**RETURN** : String - A string representation of the file size.
```
// Specifying a URL
$size = ee()->link_vault_library->file_size(array(
	'file_path'		=> 'http://example.com/storage/files/collection.zip'
));

// Specifying a full system path separate from the file
$size = ee()->link_vault_library->file_size(array(
	'directory'		=> '/the/path/to/the/folder/',
	'file_name'		=> 'celebration.mp3'
));
```
Sample output might look like:
```
11 B
24 KB
3 GB
14 TB
```

### download( $log_record_data=array() )
**RETURN** : Boolean - If the download occurred and it was logged successfully, this method returns TRUE. Otherwise, it returns FALSE.
```
$status = ee()->link_vault_library->download(array(
	'unix_time'		=> date('U'),
	'entry_id'		=> '2034',
	'member_id'		=> ee()->session->userdata('member_id'),
	'remote_ip'		=> '127.0.0.1',
	'directory'		=> '../hidden_files/',
	'file_name'		=> 'security-files.zip',
	'is_link_click'	=> 'n',
	'cf_page'		=> 'tutorials/security'
));
```

### remote_download( $log_record_data=array() )
**RETURN** : none - This method redirects to a file hosted on a remote server.
```
ee()->link_vault_library->remote_download(array(
	'unix_time'		=> date('U'),
	'member_id'		=> '4',
	'remote_ip'		=> '127.0.0.1',
	'file_name'		=> 'http://othersite.com/path/to/file.tar.gz',
	'is_link_click'	=> 'n',
	'cf_page'		=> 'downloads/other'
));
```

### s3_download( $log_record_data=array() )
**RETURN** : Void - This method redirects to the temporary S3 download path
```
ee()->link_vault_library->s3_download(array(
	'unix_time'		=> date('U'),
	'member_id'		=> ee()->session->userdata('member_id'),
	'remote_ip'		=> '127.0.0.1',
	's3_bucket'		=> 'site-downloads',
	'file_name'		=> 'my_ebook.zip',
));
```

### get_mime_type( $file_extension='' )
##### $file_extension (string)
The file extension string without the leading dot.
##### RETURN (string)
The MIME type.
```
$mime_type = ee()->link_vault_library->get_mime_type('zip');
$mime_type = ee()->link_vault_library->get_mime_type('jpg');
$mime_type = ee()->link_vault_library->get_mime_type('mp3');
```

### serve_file( $header_data=array() )
This method serves a file as a download. There is no return value.
##### $header_data (array)
An associative array of header options for the download.
```
ee()->link_vault_library->serve_file(array(
	'mime_type' => ee()->link_vault_library->get_mime_type('jpg'),
	'file_path' => '/the/full/system/path/to/birds83859.jpg',
	'file' => 'birds-wallpaper.jpg'
));

ee()->link_vault_library->serve_file(array(
	'mime_type' => ee()->link_vault_library->get_mime_type('zip'),
	'file_path' => '/the/full/system/path/to/super-long-filename-382572983584937485784.zip',
	'file' => 'your_download.zip'
));
```

### normalize_directory( $dir='' )
This method formats a system path as the path relative to the document root. This is how Link Vault stores folder paths in the DB.
##### $dir (string)
The system path to be normalized.
##### RETURN (string)
The directory as it should be stored in the Link Vault tables.
```
$dir = ee()->link_vault_library->normalize_directory('/the/system/path/public_html/downloads');
// $dir = 'downloads/';
$dir = ee()->link_vault_library->normalize_directory('/the/system/path/files');
// $dir = '../files/';
```

### distinct_download_directory_options( $table_name='downloads' )
##### $table_name (string)
The table name to search. ("downloads" or "leeches")
##### RETURN (array)
An associative array of distinct directories to directories. Yes, you read that correctly. It is primarily used by the control panel reporting tool to build an HTML select element.
```
$options = ee()->link_vault_library->distinct_download_directory_options('downloads');
$options = ee()->link_vault_library->distinct_download_directory_options('leeches');
```

### distinct_s3_bucket_options( $table_name='downloads' )
##### $table_name (string)
The table name to search. ("downloads" or "leeches")
##### RETURN (array)
An associative array of S3 buckets to S3 buckets. This method is also primarily used to construct a list of options for an HTML select element.
```
$options = ee()->link_vault_library->distinct_s3_bucket_options('downloads');
$options = ee()->link_vault_library->distinct_s3_bucket_options('leeches');
```

### distinct_pretty_urls()
##### RETURN (array)
An associative array of pretty URL IDs to their corresponding text.
```
$options = ee()->link_vault_library->distinct_pretty_urls();
```
The array will look something like:

array('1'	=> 'Google-Search-Results', '2'	=> 'Visit-My-Business-Website', '3' => 'Disguised-Affiliate-Link')

### log_download( $record_data=array() )
This method is used to create a download record without actually serving a file for download. This could be useful when importing old download records from another log.
##### $record_data (array)
This parameter should contain link_vault_downloads table column names to values.
##### RETURN (integer)
The id of the inserted log row.
```
$data = array(
	'site_id'		=> '1',
	'entry_id'		=> '1013',
	'unix_time'		=> '1366903271',
	'member_id'		=> '9',
	'remote_ip'		=> '127.0.0.1',
	'directory'		=> '../downloads/',
	'file_name'		=> 'phone-images.zip',
	'is_link_click'	=> 'n',
	'cf_my_field'	=> 'Whatever you want to store in the log'
);

$id = ee()->link_vault_library->log_download( $data );
```

### encrypt( $string='' )
This method encrypts some text using the salt value saved in the Link Vault settings.
##### $string (string)
The string to be encrypted
##### RETURN (string)
The base64-encoded encrypted string
```
$text = 'Some secret information';

$encrypted_text = ee()->link_vault_library->encrypt($text);
```

### decrypt( $string='' )
This method decrypts an encrypted string that was encrypted using the Link Vault library's encrypt method.
##### $string (string)
The base64-encoded string to be decrypted
##### RETURN (string)
Some content that was encrypted.
```
$text = ee()->link_vault_library->decrypt( $encrypted_string );
```

### fetch_boolean( $value=FALSE )
This method is used to handle the forgiveness of improper boolean parameter values.
##### $value (mixed)
The value that should be converted into a true boolean value.
##### RETURN (boolean)
```
$bool = ee()->link_vault_library->fetch_boolean('on');     // true
$bool = ee()->link_vault_library->fetch_boolean('off');    // false
$bool = ee()->link_vault_library->fetch_boolean('yes');    // true
$bool = ee()->link_vault_library->fetch_boolean('no');     // false
$bool = ee()->link_vault_library->fetch_boolean('true');   // true
$bool = ee()->link_vault_library->fetch_boolean('false');  // false
$bool = ee()->link_vault_library->fetch_boolean('1');      // true
$bool = ee()->link_vault_library->fetch_boolean('0');      // false
```

### row_search( $query_data=array(), $custom_fields=array(), $cf_exact_match=FALSE, $prefix='')
This method serves as a query builder for the Link Vault control panel reporting tool and the :records template tag. You are free to use it, but you'll probably just want to build your own queries.

##### $query_data
This parameter contains table columns and values as well as instructions for how to build the query.
##### $custom_fields
This parameter contains an associative array of Link Vault custom field column names to values.
##### $cf_exact_match
If this parameter is set to TRUE, the query will look for an exact match with the custom field parameters.
##### $prefix
Any specified prefix will be prepended to the return row column names. This is useful for template tags that may be nested within another tag pair that shares some of the same variable names.
##### RETURN (array)
The array of result rows.
```
/*
This example will return the top 5 downloads between two dates for member with ID 3405 where the download page is exactly 'downloads/music'. The results are ordered by most downloads in descending order.
*/

$data = array(
	'table'				=> 'downloads', // (downloads | leeches)
	'member_id'			=> '3405',
	'start_date'		=> 1366903271,
	'end_date'			=> 1366908135,
	'group_by'			=> 'file_name',
	'count_variable'	=> 'my_count',
	'order_by'			=> 'my_count',
	'sort'				=> 'desc',
	'limit'				=> '5'
);

$custom_fields = array(
	'cf_download_page'	=> 'downloads/music'
);

$rows = ee()->link_vault_library->row_search($data, $custom_fields, TRUE, 'lv_');
```

## Changelog

### 1.4.2 (2016-05-10)
- Fixed a bug when querying member downloads with the :download_count tag.
- Updated :records tag to include downloads and link clicks when the table parameter is omitted.

### 1.4.1 (2016-02-15)
- Updated S3 library to latest version with better time offset handling.

### 1.4.0 (2015-10-22)
- Added support for ExpressionEngine 3.
- Added :download_url template tag.
- Added :download_action template tag.
- Added link_vault_s3_timeout config variable.
- Removed need for Link Vault themes folder.

### 1.3.8 (2015-02-18)
- Added a new row_search_count() method to the PHP library.
- Improved control panel report performance for large data sets.
- Fixed a bug where the control panel reports selected the incorrect table name.

### 1.3.7 (2015-02-03)
- Fixed a session ID reference for EE 2.9+ that caused the user to log out of the control panel from the reports page.

### 1.3.6 (2014-10-10)
- Fixed installation errors that occurred on Windows systems.

### 1.3.5 (2014-07-03)
- Added pagination to the reports in the control panel.
- Added the ability to export control panel reports as XLS files.

### 1.3.4 (2014-02-03)
- Added the download_as parameter to the :download_link and :download_buttontemplate tags for renaming a file during the download.

### 1.3.3 (2013-12-16)
- Added content-disposition response header with value “attachment” to the S3 authenticated URL redirects to enforce actual file download.
- Added link_vault_s3_exclude_response_header to exclude the aforementioned response header.
- Added JavaScript to refresh the XID hash for the control panel reports (for EE 2.7.*).
- Replaced all “$this->EE” references with “ee()”.
- Raised minimum ExpressionEngine requirement to 2.7.0.

### 1.3.2 (2013-09-03)
- Updated the S3 library. (S3::getAuthenticatedURL() now uses the S3::$endpoint variable rather than the default endpoint.)
- Added link_vault_aws_endpoint config variable to override the use of the US Standard Region endpoint.
- Fixed an inconsistency between how Link Vault and Link Vault Zipper define the document root.
- Fixed a bug where Amazon S3 downloads would prepend the the default hidden folder path to the download path.

### 1.3.1 (2013-05-30)
- Added support for extremely large downloads.
- Added a second method for determining the ExpressionEngine jQuery URL.
- Added exception handling for duplicate custom fields for MSM sites.
- Fixed a bug where site_id was not being populated when logging downloads.
- Fixed a bug where the logger library was not being loaded when debug is enabled.

### 1.3.0 (2013-05-05)
- Added the Link Vault PHP library to allow developers to go beyond extensions when extending Link Vault.
- The download_model has been replaced by the aforementioned PHP library.
- Condensed most query string parameters into a single “lv” parameter for additional security.
- Added the :pretty_url template tag to generate trackable URLs that users can read and understand.
- Added the show_file_name parameter to the :download_link tag to append the file name to the URL.
- Added the variable_prefix parameter to the :records tag to prevent template variable conflicts.
- The various file_path parameters now accept URLs and full system paths in addition to relative path.
- Fixed a bug where the missing file URL was not being used to redirect users.

### 1.2.5 (2013-01-31)
- Amazon S3 files are now served via a redirect to a temporary secure URL that expires immediately.
- Created the link_vault_s3_download_start extension hook.

### 1.2.4 (2013-01-30)
- Added support for serving single files from an Amazon S3 account.
- Modified the reporting tool in the control panel to include S3 bucket selector.
- Added group_by parameter and census variable to the :records template tag pair.
- Added count_variable parameter to the :records template tag pair to override census variable name.
- Changed the Download_model::report_query method name to row_search and made it more flexible.
- The entry_id query string parameter (e) is now encrypted.
- Fixed a bug where the entry_id column was not being populated with the value supplied in the template tag parameter.
- Added expires and expires_text parameters to the :download_link and :download_button template tags.
