<?php
define('SEMANTIC_VERSION_REGEX', '/^([0-9]+)\.([0-9]+)\.([0-9]+)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/');

function increaseVersion($version, $mode) {
	preg_match(SEMANTIC_VERSION_REGEX, $version, $versionComponents);
	unset($versionComponents[0]);
	switch ($mode) {
		case 'patch':
			$versionComponents[3]++;
			break;
		case 'minor':
			$versionComponents[2]++;
			$versionComponents[3] = 0;
			break;
		case 'major':
			$versionComponents[1]++;
			$versionComponents[2] = 0;
			$versionComponents[3] = 0;
			break;
	}
	return implode('.', $versionComponents);
}

function getComposerMetadata() {
	$composerMetadata = json_decode(file_get_contents('composer.json'));
	$composerMetadata->version = isset($composerMetadata->version) ? $composerMetadata->version : '0.0.0';
	return $composerMetadata;
}

function getGithubCurl() {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, 'Release Script for ' . get('username') . '/' . get('repository'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, get('username') . ':' . get('password'));
	return $ch;
}

function getUnixStylePath($path) {
	if (strpos($path, ':') === FALSE) {
		return str_replace(array('//', '\\'), '/', $path);
	} else {
		return preg_replace('/^([a-z]{2,}):\//', '$1://', str_replace(array('//', '\\'), '/', $path));
	}
}

function readDirectoryRecursively($path, $suffix = NULL, $returnRealPath = TRUE, $returnDotFiles = FALSE, &$filenames = array()) {
	$directoryIterator = new \DirectoryIterator($path);
	$suffixLength = strlen($suffix);
	foreach ($directoryIterator as $fileInfo) {
		$filename = $fileInfo->getFilename();
		if ($filename === '.' || $filename === '..' || ($returnDotFiles === FALSE && $filename[0] === '.')) {
			continue;
		}
		if ($fileInfo->isFile() && ($suffix === NULL || substr($filename, -$suffixLength) === $suffix)) {
			$filenames[] = getUnixStylePath(($returnRealPath === TRUE ? realpath($fileInfo->getPathname()) : $fileInfo->getPathname()));
		}
		if ($fileInfo->isDir()) {
			readDirectoryRecursively($fileInfo->getPathname(), $suffix, $returnRealPath, $returnDotFiles, $filenames);
		}
	}
	return $filenames;
}

task('release:askPasswort', function(){
	$password = askHiddenResponse('Passwort for ' . get('username') . ':');
	set('password', $password);
});

task('release:increaseVersionPatch', function () {
	$composerMetadata = getComposerMetadata();
	$composerMetadata->version = increaseVersion($composerMetadata->version, 'patch');
	set('version', $composerMetadata->version);
	file_put_contents('composer.json', json_encode($composerMetadata, JSON_PRETTY_PRINT));
});

task('release:increaseVersionMinor', function () {
	$composerMetadata = getComposerMetadata();
	$composerMetadata->version = increaseVersion($composerMetadata->version, 'minor');
	set('version', $composerMetadata->version);
	file_put_contents('composer.json', json_encode($composerMetadata, JSON_PRETTY_PRINT));
});

task('release:increaseVersionMajor', function () {
	$composerMetadata = getComposerMetadata();
	$composerMetadata->version = increaseVersion($composerMetadata->version, 'major');
	set('version', $composerMetadata->version);
	file_put_contents('composer.json', json_encode($composerMetadata, JSON_PRETTY_PRINT));
});

task('release:fetchVersion', function () {
	$composerMetadata = getComposerMetadata();
	set('version', $composerMetadata->version);
});

task('release:commitComposer', function () {
	// writeln('Update and commit version in composer.json');
	runLocally('git add composer.json');
	runLocally('git commit -m "' . get('version') . '"');
});

task('release:tagRelease', function () {
	// writeln('tag current state with provided version number');
	runLocally('git tag "' . get('version') . '"');
});

task('release:pushTags', function () {
	// writeln('push tags to github');
	runLocally('git push origin master');
	runLocally('git push origin --tags');
});

task('release:removeCurrentTagFromRemote', function () {
	try {
		runLocally('git tag -d "' . get('version') . '"');
		runLocally('git push origin :refs/tags/' . get('version'));
	} catch(\Exception $e) {

	}
});

task('release:createPhar', function(){
	$pharFilename = 'Build/soup-' . get('version') . '.phar';
	if (file_exists($pharFilename)) {
		runLocally('rm ' . $pharFilename);
	}

	set('releaseFilename', $pharFilename);

	$phar = new \Phar($pharFilename, 0);

	$fileTypeIncludes = explode(',', 'php,html,css,js,eot,ttf,woff,woff2,json');
	$excludePattern = '/(Tests\/.*|Cache\/.*|vendor\/.*\/vendor)/';
	$files = [];
	foreach (readDirectoryRecursively(__DIR__) as $file) {
		$relativeFileName = str_replace(__DIR__, '', $file);

		if (preg_match($excludePattern, $relativeFileName)) {
			continue;
		}

		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $fileTypeIncludes)) {
			continue;
		}

		$files[trim($relativeFileName, '/')] = $file;
	}

	$phar->buildFromIterator(new \ArrayIterator($files));
	$phar->setStub(str_replace(
		array(
			'require __DIR__ . \'/../vendor/autoload.php\';',
			'$app = new Famelo\Soup\Application();'
		),
		array(
			'Phar::mapPhar();require \'phar://\' . __FILE__ . \'/vendor/autoload.php\';',
			'$app = new Famelo\Soup\Application("Soup", "' . get('version') . '");'
		),
		file_get_contents('bin/soup')
	));
});

task('release:updateReleasesManifest', function() {
	$manifest = array();
	if (file_exists('releases.json')) {
		$manifest = json_decode(file_get_contents('releases.json'), TRUE);
		foreach ($manifest as $key => $release) {
			if ($release['version'] == get('version')) {
				unset($manifest[$key]);
			}
		}
	}

	$sha1 = sha1_file(get('releaseFilename'));
	$file = basename(get('releaseFilename'));
	$baseUrl = 'https://github.com/' . get('username') . '/' . get('repository') . '/releases/download/';
	$manifest[] = array(
		'name' => 'soup.phar',
		'sha1' => $sha1,
		'url' => $baseUrl . get('version') . '/' . $file,
		'version' => get('version')
	);

	file_put_contents('releases.json', json_encode(array_values($manifest), JSON_PRETTY_PRINT));

	runLocally('git add releases.json');
	runLocally('git commit -m "Added Version: ' . get('version') . '"');
	runLocally('git push origin master');
});

task('release:createGithubRelease', function() {
	$ch = getGithubCurl();

	$release = array(
		'tag_name' => get('version'),
		'name' => 'Release: ' . get('version')
	);
	$uri = 'https://api.github.com/repos/' . get('username') . '/' . get('repository') . '/releases';
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($release));
	curl_setopt($ch, CURLOPT_POST, 1);

	$release = json_decode(curl_exec($ch));
	$releaseId = $release->id;
	set('releaseId', $releaseId);
});

task('release:destroyGithubRelease', function() {
	$ch = getGithubCurl();

	$uri = 'https://api.github.com/repos/' . get('username') . '/' . get('repository') . '/releases/tags/' . get('version');
	curl_setopt($ch, CURLOPT_URL, $uri);

	$release = json_decode(curl_exec($ch));
	if (!isset($release->id)) {
		return;
	}
	$releaseId = $release->id;

	$uri = 'https://api.github.com/repos/' . get('username') . '/' . get('repository') . '/releases/' . $releaseId;
	$ch = getGithubCurl();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_exec($ch);
});

task('release:addPharToRelease', function(){
	$fileName = basename(get('releaseFilename'));
	$uri = 'https://uploads.github.com/repos/' . get('username') . '/' . get('repository') . '/releases/' . get('releaseId') . '/assets?name=' . $fileName;

	$ch = getGithubCurl();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/plain"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents(get('releaseFilename')));
	curl_setopt($ch, CURLOPT_POST, 1);

	$result = curl_exec($ch);
	curl_close($ch);
});


set('username', 'mneuhaus');
set('repository', 'soup');

task('release:patch', [
	'release:askPasswort',
    'release:increaseVersionPatch',
    'release:commitComposer',
    'release:tagRelease',
    'release:pushTags',
    'release:createPhar',
    'release:updateReleasesManifest',
    'release:createGithubRelease',
    'release:addPharToRelease'
]);

task('release:minor', [
	'release:askPasswort',
    'release:increaseVersionMinor',
    'release:commitComposer',
    'release:tagRelease',
    'release:pushTags',
    'release:createPhar',
    'release:updateReleasesManifest',
    'release:createGithubRelease',
    'release:addPharToRelease'
]);

task('release:major', [
	'release:askPasswort',
    'release:increaseVersionMajor',
    'release:commitComposer',
    'release:tagRelease',
    'release:pushTags',
    'release:createPhar',
    'release:updateReleasesManifest',
    'release:createGithubRelease',
    'release:addPharToRelease'
]);

task('release:replaceCurrent', [
	'release:askPasswort',
    'release:fetchVersion',
    'release:destroyGithubRelease',
    'release:removeCurrentTagFromRemote',
    'release:tagRelease',
    'release:pushTags',
    'release:createPhar',
    'release:updateReleasesManifest',
    'release:createGithubRelease',
    'release:addPharToRelease'
]);

task('release:build', [
	'release:fetchVersion',
    'release:createPhar',
]);