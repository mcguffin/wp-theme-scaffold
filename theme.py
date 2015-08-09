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
		config['theme_slug'] 		= slugify(config['theme_name'])
		config['wp_theme_slug']		= slugify(config['theme_name'],'-')
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
#					continue
					fin = codecs.open( source , 'rb' , encoding='utf-8' )
					content = fin.read()
					fin.close()
				
					content = self._substitute(content);
					fout = codecs.open( target , 'wb' , encoding='utf-8' )
					fout.write(content);
					fout.close()
				else:
					shutil.copyfile(source , target)
			
		# 
		pass
	
	
	def _substitute(self, str):
		repl = {
			"'_bs'"				: "'%s'" % self.config['theme_slug'],
			'_s_'				: '%s_' % self.config['theme_slug'],
			"Text Domain: _s"	: 'Text Domain: %s' % self.config['theme_slug'],
			"&nbsp;_s"			: "&nbsp;%s" % self.config['theme_slug'],
			'_s-'				: '%s-' % self.config['theme_slug']
		}
		for s in repl:
#			print s
 			str = str.replace(s,repl[s])
		return str
	

usage = '''
usage ./theme.py 'Theme Name' options
    options can be any of:
        --force         Override existing plugin
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
	print 'Plugin exists:',result
	print 'use --force to override existing plugin'
