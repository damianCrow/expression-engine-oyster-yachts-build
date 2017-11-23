<?php

if (mail('unklegee@gmail.com', 'Quick email test', 'Is this on?'))
{
    echo 'Mail sent successfully.';
}
else
{
    echo 'Mail was not sent.';
}
?>