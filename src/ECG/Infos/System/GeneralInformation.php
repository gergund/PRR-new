<?php

namespace ECG\Infos\System;

use ECG\Infos\InformationInterface;
use ECG\Util\ArrayReader;

class GeneralInformation implements InformationInterface
{
    /**
     * Array reader.
     *
     * @var ArrayReader
     */
    private $reader = null;

    /**
     * Construct.
     *
     * @param array $config
     */
    public function __construct()
    {
        $this->reader = new ArrayReader();
    }

    /**
     * Get dataset title.
     *
     * @return string
     */
    public function getName()
    {
        return 'System (OS) Configuration';
    }

    /**
     * Collect information about operation system.
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        $data['Platform'] = $this->getPlatform();
        $data['Release'] = $this->getRelease();
        $data['Kernel'] = $this->getKernel();
        $data['OS Architecture'] = $this->getSystemArchitecture();
        $data['CPU Architecture'] = $this->getHardwareArchitecture();
        $data['Threading'] = $this->getThreading();
        $data['Compiler'] = $this->getCompiler();
        $data['SE Linux'] = $this->getSelinux();
        $data['Virtualization'] = $this->getVirtualized();
        $data['Processors'] = $this->getProcessors();
        $data['CPU Model'] = $this->getCpuModel();
        $data['Memory'] = $this->getMemory();
        $data['HTTP Server'] = $this->getHttpServer();
        $data['Hostname'] = $this->getHostname();
        return $data;
    }

    /**
     * Get platform name.
     *
     * @return string
     */
    private function getPlatform()
    {
        $file = '/proc/version';

        if (!is_file($file) || !is_readable($file)) {
            return 'Unknown';
        }

        $contents = file_get_contents($file);
        if (preg_match('/Linux/', $contents, $match) != 1) {
            return 'Unknown';
        }

        return $match[0];
    }

    /**
     * Get release name.
     *
     * @return string
     */
    private function getRelease()
    {
        $systemFilePath = '/etc/system-release';
        if (is_file($systemFilePath) && is_readable($systemFilePath)) {
            $release = trim(file_get_contents($systemFilePath));
            return $release;
        }

        $issueFilePath = '/etc/issue.net';
        if (is_file($issueFilePath) && is_readable($issueFilePath)) {
            $release = trim(file_get_contents($issueFilePath));
            if (strlen($release)) {
                return $release;
            }
        }

        return 'Unknown';
    }

    /**
     * Get OS architecture type.
     *
     * @return string
     */
    private function getSystemArchitecture()
    {
        $versionFilePath = '/proc/version';

        if (!is_file($versionFilePath) || !is_readable($versionFilePath)) {
            return 'Unknown';
        }

        $content = file_get_contents($versionFilePath);
        if (preg_match('/(amd64|x86_64)/', $content, $match) != 1) {
            $lscpu = shell_exec('lscpu 2>&1');
            $lscpu = trim($lscpu);

            if (preg_match('/Architecture:\s+(\S+)/', $lscpu, $match) != 1) {
                return 'Unknown';
            }
        }

        if ($match[0] == 'amd64' || $match[0] == 'x86_64' || $match[1] == 'x86_64') {
            return '64-bit';
        }

        return 'Unknown';
    }

    /**
     * Get hardware architecture type.
     *
     * @return string
     */
    private function getHardwareArchitecture()
    {
        $cpuinfoFilePath = '/proc/cpuinfo';

        if (!is_file($cpuinfoFilePath) || !is_readable($cpuinfoFilePath)) {
            return 'Unknown';
        }

        $content = file_get_contents($cpuinfoFilePath);
        if (preg_match('/lm/', $content, $match) != 1) {
            return 'Unknown';
        }

        if ($match[0] == 'lm') {
            return '64-bit';
        }

        return 'Unknown';
    }

    /**
     * Get threading.
     *
     * @return string
     */
    private function getThreading()
    {
        $content = shell_exec('getconf -a | grep GNU_LIBPTHREAD_VERSION 2>&1');
        $content = trim($content);

        if (preg_match('/GNU_LIBPTHREAD_VERSION             (\S+ *.*)/', $content, $match) != 1) {
            return 'Unknown';
        }

        return $match[1];
    }

    /**
     * Get compiler.
     *
     * @return string
     */
    private function getCompiler()
    {
        $content = shell_exec('gcc -v 2>&1');
        $content = trim($content);

        if (preg_match('/gcc version (.*)/', $content, $match) != 1) {
            return 'Unknown';
        }

        return $match[0];
    }

    /**
     * Get kernel.
     *
     * @return string
     */
    private function getKernel()
    {
        $versionFilePath = '/proc/version';

        if (!is_file($versionFilePath) || !is_readable($versionFilePath)) {
            return 'Unknown';
        }

        $content = file_get_contents($versionFilePath);
        if (preg_match('/^Linux version (\S+).+$/', $content, $match) != 1) {
            return 'Unknown';
        }

        return $match[1];
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    private function getHostname()
    {
        $hostnameFilePath = '/proc/sys/kernel/hostname';

        if (!is_file($hostnameFilePath) || !is_readable($hostnameFilePath)) {
            return 'Unknown';
        }

        $hostname = trim(file_get_contents($hostnameFilePath));
        if (strlen($hostname)) {
            return $hostname;
        }

        return 'Unknown';
    }

    /**
     * Get SE Linux info.
     *
     * @return string
     */
    private function getSelinux()
    {
        $content = shell_exec('getenforce 2>&1');
        $content = trim($content);

        if (preg_match('/(Enforcing|Permissive|Disabled)/', $content, $match) != 1) {
            return 'No SELinux detected';
        }

        return $match[0];
    }

    /**
     * Chech if command exists.
     *
     * @param string $cmd
     * @return boolean
     */
    private function commandExists($cmd)
    {
        $return = shell_exec(sprintf("which %s 2>&1", escapeshellarg($cmd)));
        return !empty($return);
    }

    /**
     * Get virtualization info.
     *
     * @return string
     */
    private function getVirtualized()
    {
        if ($this->commandExists('lspci')) {
            $content = shell_exec('lspci 2>&1');
            $content = trim($content);

            if (preg_match('/(virtualbox|xen|vmware|kvm)/i', $content, $match) != 1) {
                return 'No Virtualization detected';
            }

            return $match[0];
        }

        if ( $this->commandExists('hostnamectl')) {
            $content = shell_exec('hostnamectl 2>&1 | grep Virtualization');
            $content = trim($content);

            if (preg_match('/Virtualization: (\S+)/', $contents, $match) != 1) {
                return 'No Virtualization detected';
            }

            return $match[1];
        }

        return 'No Virtualization detected';
    }

    /**
     * Get processors.
     *
     * @return string
     */
    private function getProcessors()
    {
        $physical = shell_exec('grep \'physical id\' /proc/cpuinfo | sort -u | wc -l');
        $physical = trim($physical);

        $cores = shell_exec('grep \'cpu cores\' /proc/cpuinfo | head -n 1 | cut -d: -f2');
        $cores = trim($cores);

        $virtual = shell_exec('grep -c \'^processor\' /proc/cpuinfo');
        $virtual = trim($virtual);

        if ($cores > 0 && $physical * $cores < $virtual) {
            $hyperthreading = 'yes';
        } else {
            $hyperthreading = 'no';
        }

        $value = sprintf(
            "physical = %s, cores = %s, virtual = %s, hyperthreading = %s",
            $physical,
            $cores,
            $virtual,
            $hyperthreading
        );
        return $value;
    }

    /**
     * Get CPU model.
     *
     * @return string
     */
    private function getCpuModel()
    {
        $numCpu = shell_exec('grep \'processor\' /proc/cpuinfo | wc -l');
        $numCpu = trim($numCpu);

        $model = shell_exec('grep \'model name\' /proc/cpuinfo | head -n 1 | cut -d: -f2');
        $model = trim($model);

        return sprintf('%s x %s', $numCpu, $model);
    }

    /**
     * Get memory info.
     *
     * @return string
     */
    private function getMemory()
    {
        $content = shell_exec('grep \'MemTotal\' /proc/meminfo');
        $content = trim($content);

        if (preg_match('/MemTotal:\s+(\d+)/', $content, $match) != 1) {
            return 'Unknown';
        }

        return sprintf('%s GB', round($match[1] / 1024 / 1024));
    }

    /**
     * Detect Apache server.
     *
     * @return boolean
     */
    private function detectApache()
    {
        $content = shell_exec('ps aux');
        $content = trim($content);

        if (preg_match('/(apache|httpd)/', $content, $match) == 1) {
            return true;
        }

        return false;
    }

    /**
     * Detect NGINX server.
     *
     * @return boolean
     */
    private function detectNginx()
    {
        $content = shell_exec('ps aux');
        $content = trim($content);

        if (preg_match('/(nginx)/', $content, $match) == 1) {
            return true;
        }

        return false;
    }

    /**
     * Get command path.
     *
     * @param string $cmd
     * @return string|null
     */
    private function whereis($cmd)
    {
        $content = shell_exec(sprintf("whereis %s 2>&1", escapeshellarg($cmd)));

        $pattern = '/' . $cmd . ':\s(\S+)/';

        if (preg_match($pattern, $content, $match) == 1) {
            return $match[1];
        }

        return null;
    }

    /**
     * Get HTTP server.
     *
     * @return string
     */
    private function getHttpServer()
    {
        if ($this->detectApache() == true && !is_null($this->whereis('apachectl'))) {
            $cmd = $this->whereis('apachectl') . ' -V 2>&1';
            $content = shell_exec($cmd);
            $content = trim($content);

            if (preg_match('/Server version:\s(\S+)/', $content, $match) == 1) {
                return $match[1];
            }
        }

        if ($this->detectNginx() == true && !is_null($this->whereis('nginx'))) {
            $cmd = $this->whereis('nginx') . ' -V 2>&1';
            $content = shell_exec($cmd);
            $content = trim($content);

            if (preg_match('/nginx version:\s(\S+)/', $content, $match) == 1) {
                return $match[1];
            }
        }

        return 'Not Found';
    }
}
