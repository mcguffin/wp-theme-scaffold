#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, os, shutil, pystache, re, pwd, pprint, shutil,codecs,subprocess
from datetime import date
from pprint import pprint

# def rm_wp(str):
# 	return re.sub(r'(?i)^(WP|WordPress\s?)','',str).strip()
# 
def slugify(str,separator='_'):
	return re.sub(r'\s',separator,str.strip()).lower()

# def plugin_slug(str):
# 	return slugify(rm_wp(str))
# 
# def plugin_classname(str):
# 	return ''.join(x for x in rm_wp(str).title() if not x.isspace())

class wp_theme:
	defaults = {
		'theme_name'	: '',
		'theme_slug'	: '',
		'grid_columns'	: 12,
		'screen_sizes'	: 'xs,sm,md,lg'
	}

	def __init__(self,config):
		self.config			= self.process_config(config)
		self.theme_dir		= os.getcwd()+'/'+slugify(self.config['theme_name'],'-')
		self.theme_source	= os.path.dirname(os.path.realpath(__file__))+'/_bs/'
	
	def process_config(self,config):
		author 						= pwd.getpwuid( os.getuid() ).pw_gecos
		
		config['theme_author'] 		= author.decode('utf-8')#.encode('utf-8')
		config['this_year'] 		= date.today().year

		config['theme_name'] 		= config['theme_name']
		config['theme_slug'] 		= slugify(config['theme_name'])
		config['theme_slug_dash']	= slugify(config['theme_name'],'-')
		
		
		
		return config
	
	def make(self):
		try:
			os.mkdir(self.theme_dir)
		except OSError as e:
			return e

		ignore = []
		f = codecs.open(self.theme_source+'.gitignore','rb',encoding='utf-8')
		ignore = f.readlines()
		f.close()
		ignore = [x.replace('\n','') for x in ignore if len(x) > 0 and x[0] != '#']
		ignore.append('.git/')
		ignore.append('.gitignore')
		subst = ['php','md','scss','js','css','txt']
#		print ignore
		for root, subdirs, files in os.walk(self.theme_source):
			relroot = root.replace(self.theme_source,'') + '/'
			
			# ignore files
			if [x for x in ignore if relroot.find(x) >= 0]:
				continue;

			dir = self.theme_dir + '/' + relroot

			# make subdirs if needed
			if not os.path.exists(dir):
				os.makedirs(dir)

			# copy file and substitute _bs
			for file in files:
				if [x for x in ignore if file.find(x) >= 0]:
					continue;
				source = root + '/' + file
				target = dir + '/' + file
#				print [1 for x in subst if file.match];
				print file
				if  [x for x in subst if re.findall('\.'+x+'$',file)]:

					content = pystache.render( self._read_file_contents(source),self.config)
					
					fout = codecs.open( target , 'wb' , encoding='utf-8' )
					fout.write(content);
					fout.close()
				else:
					shutil.copyfile(source , target)
			
		# 
		pass
	
	
	def _read_file_contents( self , file_path ):
		if not os.path.exists(file_path):
			return ''
		f = codecs.open(file_path,'rb',encoding='utf-8')
		contents = f.read()
		f.close()
		return contents


	def _substitute(self, str):
		repl = {
			"'_bs'"				: "'%s'" % self.config['theme_slug'],
			'_bs_'				: '%s_' % self.config['theme_slug_dash'],
			"Text Domain: _bs"	: 'Text Domain: %s' % self.config['theme_slug_dash'],
#			"&nbsp;_bs"			: "&nbsp;%s" % self.config['theme_slug'],
			'_bs-'				: '%s-' % self.config['theme_slug']
		}
		for s,r in repl.iteritems():
#			print s
 			str = str.replace(s,r)
		return str
	

usage = '''
usage ./theme.py 'Theme Name' options
    options can be any of:
        --force         Override existing theme
        grid_columns:<int>		Number of grid columns default 12
        screen_sizes:<sizes>	Comma separated screen sizes. Default xs,sm,md,lg
'''

defaults = config = wp_theme.defaults

try:
	config['theme_name']	= sys.argv[1]
except IndexError as e:
	print usage
	sys.exit(0)


print "Generating Theme:", config['theme_name']
maker = wp_theme(config)
if '--force' in sys.argv and os.path.exists(maker.theme_dir):
	shutil.rmtree( maker.theme_dir )
result = maker.make()

if isinstance(result, Exception):
	print 'Theme exists:',result
	print 'use --force to override existing theme'
else:
	print 'Compiling scss:'
	subprocess.call(["sass", '{path:s}/sass/style.scss'.format( path = maker.theme_dir ), '{path:s}/style.css'.format( path = maker.theme_dir ), ' --style', 'compressed', '--precision', '8', '--trace' ])
	subprocess.call(["sass", '{path:s}/sass/editor-style.scss'.format( path = maker.theme_dir ), '{path:s}/editor-style.css'.format( path = maker.theme_dir ), ' --style', 'compressed', '--precision', '8', '--trace' ])
	print 'done'
