<?php

require_once('inc/PCRedit.php');

$a = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);
$b = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);

echo "A:\n";
echo var_dump($a);
echo "\n B:\n";
echo var_dump($b);
echo "\n";

echo "arrays are different? ".(PCR_arrays_differ("Pkey", $a, $b) ? "TRUE" : "FALSE")."\n";

$foo = fgets(STDIN);

$a = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);
$b = array("chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0);

echo "\n\n\nA:\n";
echo var_dump($a);
echo "\n B:\n";
echo var_dump($b);
echo "\n";

echo "arrays are different? ".(PCR_arrays_differ("Pkey", $a, $b) ? "TRUE" : "FALSE")."\n";

$foo = fgets(STDIN);

$a = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);
$b = array("RunNumber" => 2010124, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);

echo "\n\n\nA:\n";
echo var_dump($a);
echo "\n B:\n";
echo var_dump($b);
echo "\n";

echo "arrays are different? ".(PCR_arrays_differ("Pkey", $a, $b) ? "TRUE" : "FALSE")."\n";

$foo = fgets(STDIN);

$a = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);
$b = array("RunNumber" => 2010123, "foobar" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);

echo "A:\n";
echo var_dump($a);
echo "\n B:\n";
echo var_dump($b);
echo "\n";

echo "arrays are different? ".(PCR_arrays_differ("Pkey", $a, $b) ? "TRUE" : "FALSE")."\n";

$foo = fgets(STDIN);

$a = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);
$b = array("RunNumber" => 2010123, "chief_complaint" => "Difficulty breathing", "time_of_onset" => "this morning", "aid_given_by" => "PD", "remarks" => "Foo Bar Baz Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elit neque, hendrerit at lacinia ac, commodo volutpat lacus. Aliquam a purus vitae nunc rhoncus viverra sit amet vel purus. Suspendisse at urna eget ipsum tincidunt pretium quis sit amet libero. Ut mattis purus ut enim venenatis vitae dapibus erat fringilla. Quisque sed ipsum ipsum. Nulla facilisi. Vestibulum rhoncus dictum interdum. Sed hendrerit velit in diam convallis vehicula. Nunc laoreet, sem nec rutrum egestas, mi dolor ultrices purus, nec auctor est lorem vitae felis. Nam lacinia rhoncus augue, a auctor justo sollicitudin sed. Proin iaculis ornare iaculis. Curabitur porta tristique aliquam. Sed massa lorem, pharetra id blandit a, congue eget quam.", "has_loss_of_consc" => 1, "DOB" => '2001-02-16', "is_duty_call" => 0, "is_driver_to_hosp" => 1);

echo "A:\n";
echo var_dump($a);
echo "\n B:\n";
echo var_dump($b);
echo "\n";

echo "arrays are different? ".(PCR_arrays_differ("Pkey", $a, $b) ? "TRUE" : "FALSE")."\n";

?>