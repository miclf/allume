<?php namespace Miclf\Allume;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;

/**
 * Initialize a new Laravel project.
 *
 * @author Michaël Lecerf <michael@estsurinter.net>
 */
class LaravelCommand extends Command
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('laravel')
            ->setDescription('Initialize a new Laravel project.')
        ;
    }

    /**
     * Input interface of the command.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * Output interface of the command.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Question helper.
     *
     * @var \Symfony\Component\Console\Helper\HelperInterface
     */
    protected $questionHelper;

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface    $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return null|int  null or 0 if everything went fine, an error code otherwise.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $this->questionHelper = $this->getHelper('question');

        // Check if the command is executed from the person’s home directory.
        if (!@chdir('./code')) {
           $output->writeln("<error>This has to be executed from the home directory</error>");
           exit(1);
        }

        $vendor = $this->ask('Vendor name', 'miclf');
        $name   = $this->ask('Project name', 'project');

        $defaultPath = $name;

        if (file_exists('./'.$defaultPath)) {
            $defaultPath .= '-2';
        }

        $path        = $this->ask('Directory', $defaultPath);
        $description = $this->ask('Description of the project');

        // Determine the absolute path to this user’s home directory.
        $segments = explode('/', exec('pwd'));
        array_pop($segments);
        $homePath = implode('/', $segments);

        $license = $this->askForLicense();

        $directory = "./{$name}";

       if (file_exists($directory)) {
           $output->writeln("<error>Directory {$directory} already exists</error>");
           exit(2);
       }

        $this->createProject($name);
    }

    /**
     * Ask for something via prompt.
     *
     * @param  string  $question
     * @param  mixed   $defaultAnswer
     *
     * @return string  The person’s answer
     */
    protected function ask($question, $defaultAnswer = null)
    {
        $questionString = "<info>{$question}:</info> ";

        if (!is_null($defaultAnswer)) {
            $questionString = "<info>{$question} <comment>({$defaultAnswer})</comment>:</info> ";
        }

        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new Question($questionString, $defaultAnswer)
        );
    }

    /**
     * Ask the person which license she wants for the project.
     *
     * @return string
     */
    protected function askForLicense()
    {
        $question = new Question(
            '<info>License</info> <comment>(CC0-1.0)</comment><info>:</info> ',
            'CC0-1.0'
        );

        $licenses = ['AGPL', 'CC0-1.0', 'MIT', 'proprietary'];

        $question->setAutocompleterValues($licenses);

        $helper = $this->getHelper('question');

        return $helper->ask($this->input, $this->output, $question);
    }

    /**
     * Create a new laravel/laravel project using Composer.
     *
     * @param string  $path  Path to the project, relative to the code directory
     */
    protected function createProject($path)
    {
        $command =
            'composer create-project'.

            // Do not output any message. Since this command is part of a
            // bigger process, we don’t want its outpout to pollute
            // what we’ll generate from this other process.
            ' --quiet'.

            // Ignore platform requirements (php & ext- packages), so that
            // this tool can also be used from environments that are
            // not the one that will be used for development.
            ' --ignore-platform-reqs'.

            // Forces installation from package dist.
            ' --prefer-dist'.

            // Command arguments. The path of the new project will be used
            // to make the directory where the files should be created.
            ' laravel/laravel '.
            ' '.escapeshellarg($path);

        $this->output->writeln(
            "- <info>Creating project</info> in <comment>~/code/{$path}.</comment>".
            ' It may take a while. Perfect time to prepare some tea.'
        );

        exec($command);
    }
}
